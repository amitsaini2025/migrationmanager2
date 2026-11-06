<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ClientMatter;
use App\Models\Document;
use App\Models\Note;
use App\Models\Staff;
use Illuminate\Support\Facades\Log;

/**
 * Adds one Action-page Checklist row when a client document is fully signed.
 * Signing, the signed file, activity log, and bell notification are left unchanged.
 */
class SignedDocumentChecklistActionService
{
    public function record(Document $document): void
    {
        try {
            $this->createChecklistAction($document);
        } catch (\Throwable $e) {
            Log::error('Failed to create Checklist action for signed document', [
                'document_id' => $document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function createChecklistAction(Document $document): void
    {
        if ((int) $document->id <= 0 || (int) $document->client_id <= 0) {
            return;
        }

        if ((string) $document->status !== 'signed') {
            return;
        }

        $groupId = 'signed-doc-'.$document->id;
        if (Note::query()->where('unique_group_id', $groupId)->exists()) {
            return;
        }

        $matter = null;
        if ((int) $document->client_matter_id > 0) {
            $matter = ClientMatter::query()
                ->select(['id', 'client_unique_matter_no', 'sel_person_responsible', 'sel_person_assisting'])
                ->find($document->client_matter_id);
        }

        $assigneeIds = $this->assigneeIds($document, $matter);
        if ($assigneeIds === []) {
            Log::warning('No staff assignee for signed-document Checklist action', [
                'document_id' => $document->id,
                'client_matter_id' => $document->client_matter_id,
            ]);

            return;
        }

        $client = Admin::query()->select(['id', 'client_id'])->find($document->client_id);
        $description = $this->description($document, $client, $matter);
        $assignerId = $this->assignerId($matter, $assigneeIds);

        foreach ($assigneeIds as $assignedTo) {
            $action = new Note;
            $action->user_id = $assignerId;
            $action->client_id = (int) $document->client_id;
            $action->matter_id = $matter?->id;
            $action->assigned_to = $assignedTo;
            $action->description = $description;
            $action->action_date = now()->toDateString();
            $action->task_group = 'Checklist';
            $action->type = 'client';
            $action->is_action = 1;
            $action->status = '0';
            $action->pin = 0;
            $action->unique_group_id = $groupId;
            $action->save();
        }
    }

    /**
     * @return list<int>
     */
    private function assigneeIds(Document $document, ?ClientMatter $matter): array
    {
        $ids = [];
        if ($matter !== null) {
            foreach (['sel_person_responsible', 'sel_person_assisting'] as $field) {
                $id = (int) ($matter->{$field} ?? 0);
                if ($id > 0) {
                    $ids[$id] = true;
                }
            }
        }

        $assigneeIds = $this->existingStaffIds(array_keys($ids));
        if ($assigneeIds !== []) {
            return $assigneeIds;
        }

        return $this->existingStaffIds([
            (int) ($document->user_id ?? 0),
            (int) ($document->created_by ?? 0),
        ]);
    }

    /**
     * @param  list<int>  $ids
     * @return list<int>
     */
    private function existingStaffIds(array $ids): array
    {
        $unique = [];
        foreach ($ids as $id) {
            if ($id > 0) {
                $unique[$id] = true;
            }
        }

        if ($unique === []) {
            return [];
        }

        return Staff::query()
            ->whereIn('id', array_keys($unique))
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * Action page Assigner Name reads user_id. These rows show Person Assisting.
     * Person Responsible is used only when the matter has no assisting staff.
     *
     * @param  list<int>  $assigneeIds
     */
    private function assignerId(?ClientMatter $matter, array $assigneeIds): int
    {
        $assistingId = (int) ($matter->sel_person_assisting ?? 0);
        if (in_array($assistingId, $assigneeIds, true)) {
            return $assistingId;
        }

        $responsibleId = (int) ($matter->sel_person_responsible ?? 0);
        if (in_array($responsibleId, $assigneeIds, true)) {
            return $responsibleId;
        }

        return $assigneeIds[0];
    }

    private function description(Document $document, ?Admin $client, ?ClientMatter $matter): string
    {
        $clientCode = trim((string) ($client->client_id ?? ''));
        $matterNo = trim((string) ($matter->client_unique_matter_no ?? ''));

        if ($clientCode !== '' && $matterNo !== '') {
            $reference = $clientCode.'-'.$matterNo;
        } elseif ($clientCode !== '') {
            $reference = $clientCode;
        } elseif ($matterNo !== '') {
            $reference = $matterNo;
        } else {
            $reference = 'Client';
        }

        $label = (string) $document->doc_type === 'agreement'
            ? 'the service agreement'
            : $this->documentLabel($document);

        return htmlspecialchars($reference.' signed '.$label, ENT_QUOTES, 'UTF-8');
    }

    private function documentLabel(Document $document): string
    {
        $checklist = trim(str_replace("\0", '', (string) ($document->checklist ?? '')));
        if ($checklist !== '') {
            return $checklist;
        }

        $fileName = trim((string) ($document->file_name ?? ''));
        if ($fileName !== '') {
            return $fileName;
        }

        return 'the document';
    }
}
