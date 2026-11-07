<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\ClientMatter;
use App\Models\Document;
use App\Models\WorkflowStage;
use App\Support\ClientDetailDocumentsTab;
use App\Support\WorkflowStageNavigation;
use App\Support\WorkflowV2Display;
use Illuminate\Support\Facades\Log;

class SignedDocumentFinalizer
{
    /**
     * Create the visa or nomination signed copy, archive the unsigned parent, and advance the matter.
     * Call only after the document status has become signed.
     */
    public function finalize(Document $document, string $signedPdfUrl): void
    {
        // For visa/nomination documents: create {checklist}_signed row in same category (if not already exists).
        // Refresh so folder_name / checklist / matter match DB after save (avoid stale model skipping this block).
        $document->refresh();

        $docTypeNorm = strtolower(trim((string) ($document->doc_type ?? '')));
        $folderGuard = trim((string) ($document->folder_name ?? ''));
        $baseChecklist = trim((string) str_replace("\0", '', (string) ($document->checklist ?? '')));
        if ($baseChecklist === '') {
            $baseChecklist = 'Document';
        }
        $checklistAlreadySignedSuffix = str_ends_with($baseChecklist, '_signed');
        $isVisaOrNominationDoc = in_array($docTypeNorm, ['visa', 'nomination'], true);

        $shouldCreateSignedCopy = $isVisaOrNominationDoc
            && (bool) $document->client_id
            && $folderGuard !== ''
            && ! $checklistAlreadySignedSuffix;

        if ($isVisaOrNominationDoc && ! $shouldCreateSignedCopy) {
            Log::info('Skipping _signed document row', [
                'document_id' => $document->id,
                'doc_type' => $document->doc_type,
                'has_client_id' => (bool) $document->client_id,
                'folder_name_raw' => $document->folder_name,
                'folder_empty_after_trim' => $folderGuard === '',
                'checklist' => $document->checklist,
                'checklist_ends_with__signed' => $checklistAlreadySignedSuffix,
            ]);
        }

        if ($shouldCreateSignedCopy) {
            $signedChecklist = $baseChecklist.'_signed';
            $exists = Document::where('client_id', '=', $document->client_id, 'and')
                ->where('folder_name', '=', $document->folder_name, 'and')
                ->where('client_matter_id', '=', $document->client_matter_id, 'and')
                ->where('checklist', '=', $signedChecklist, 'and')
                ->whereNull('not_used_doc')
                ->exists();

            $companionReady = $exists;

            if ($exists) {
                Log::info('Signed companion row already present (active)', [
                    'document_id' => $document->id,
                    'doc_type' => $docTypeNorm,
                    'signed_checklist' => $signedChecklist,
                    'folder_name' => $document->folder_name,
                    'client_matter_id' => $document->client_matter_id,
                ]);
            }

            if (! $exists) {
                try {
                    $parentKey = (string) ($document->myfile_key ?? '');
                    $parentStem = preg_replace('/\.(pdf|PDF)$/', '', $document->file_name ?? 'document');
                    if ($parentKey !== '' && ! preg_match('/^\d+_signed\.pdf$/i', $parentKey)) {
                        $signedKey = pathinfo($parentKey, PATHINFO_FILENAME).'_signed.pdf';
                    } else {
                        $signedKey = $parentStem.'_signed.pdf';
                    }
                    $signedKey = preg_replace('/[^a-zA-Z0-9_\-\.\s\$]+/', '_', $signedKey);
                    $signedKey = trim($signedKey);
                    if ($signedKey === '' || strcasecmp($signedKey, '_signed.pdf') === 0 || strlen($signedKey) > 200) {
                        $signedKey = mb_substr(preg_replace('/[^a-zA-Z0-9_\-\.\s\$]+/', '_', $parentStem.'_signed.pdf'), 0, 200);
                    }
                    if ($signedKey === '' || strcasecmp($signedKey, '_signed.pdf') === 0) {
                        $signedKey = $document->id.'_signed.pdf';
                    }
                    $signedDoc = new Document;
                    $signedDoc->checklist = $signedChecklist;
                    $signedDoc->file_name = preg_replace('/\.(pdf|PDF)$/', '', $document->file_name ?? 'document').'_signed';
                    $signedDoc->filetype = 'pdf';
                    $signedDoc->myfile = $signedPdfUrl;
                    $signedDoc->myfile_key = $signedKey;
                    $signedDoc->client_id = $document->client_id;
                    $signedDoc->client_matter_id = $document->client_matter_id;
                    $signedDoc->folder_name = (string) $document->folder_name;
                    // Keep companion in the same tab as the parent (visa vs nomination).
                    $signedDoc->doc_type = $docTypeNorm;
                    $signedDoc->type = 'client';
                    $signedDoc->user_id = $document->user_id;
                    $signedDoc->status = 'signed';
                    $signedDoc->signed_doc_link = $signedPdfUrl;
                    $signedDoc->save();
                    $companionReady = true;
                    Log::info('Created _signed companion row', [
                        'original_id' => $document->id,
                        'signed_id' => $signedDoc->id,
                        'doc_type' => $docTypeNorm,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create _signed companion row', [
                        'document_id' => $document->id,
                        'doc_type' => $docTypeNorm,
                        'signed_checklist' => $signedChecklist ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            // Hide unsigned parent from Forms/Visa once the signed companion is active.
            // Does not delete the file; parent moves to Not Used (same as manual action).
            if ($companionReady && ClientDetailDocumentsTab::archiveUnsignedParentAfterSignedCompanion($document)) {
                Log::info('Archived unsigned parent after signed companion', [
                    'document_id' => $document->id,
                    'doc_type' => $docTypeNorm,
                    'signed_checklist' => $signedChecklist,
                ]);
            }
        }

        // Advance workflow to next stage when document is tied to a checklist/client matter
        if ($document->client_matter_id && $document->client_id) {
            try {
                ClientMatter::where('id', '=', $document->client_matter_id, 'and')->update(['updated_at_type' => 'signed', 'updated_at' => now()]);

                // Agreement-specific activity log (Cost Agreement)
                if ($document->doc_type === 'agreement') {
                    $clientMatterInfo = ClientMatter::select(['client_unique_matter_no', 'sel_person_responsible'])
                        ->where('id', '=', $document->client_matter_id)->first();
                    if ($clientMatterInfo && $clientMatterInfo->sel_person_responsible) {
                        $docSignerInfo = Admin::select(['first_name', 'last_name', 'client_id'])
                            ->where('id', '=', $document->client_id)->first();
                        $docSignerFullName = $docSignerInfo ? trim(($docSignerInfo->first_name ?? '').' '.($docSignerInfo->last_name ?? '')) : 'NA';
                        $docSignerClientId = $docSignerInfo?->client_id ?? 'NA';
                        $clientMatterReference = $docSignerClientId.'-'.($clientMatterInfo->client_unique_matter_no ?? '');
                        $signedDocName = $document->getFilenameWithExtensionForDisplay();
                        $subject = $docSignerFullName.' signed cost agreement for matter ref no - '.$clientMatterReference.' at document '.$signedDocName;
                        ActivitiesLog::create([
                            'client_id' => $document->client_id,
                            'created_by' => $clientMatterInfo->sel_person_responsible,
                            'description' => '',
                            'subject' => $subject,
                            'activity_type' => 'document',
                            'task_status' => 0,
                            'pin' => 0,
                        ]);
                    }
                }

                // Advance workflow stage to next in sequence (when next stage doesn't require manual input)
                $clientMatter = ClientMatter::find($document->client_matter_id, ['*']);
                if ($clientMatter && $clientMatter->workflow_stage_id) {
                    // Do not auto-advance discontinued matters (same policy as staff stage transitions)
                    if ((int) $clientMatter->matter_status === 0) {
                        Log::info('Skipped workflow advance on sign: matter discontinued', [
                            'document_id' => $document->id,
                            'client_matter_id' => $document->client_matter_id,
                        ]);
                    } else {
                        $currentStage = WorkflowStage::find($clientMatter->workflow_stage_id, ['*']);
                        if ($currentStage) {
                            // Match staff next-stage: block advance while required checklist items remain
                            $outstandingRequired = WorkflowV2Display::outstandingRequiredForCurrentStage($clientMatter);
                            if ($outstandingRequired > 0) {
                                Log::info('Skipped workflow advance on sign: outstanding required checklist items', [
                                    'document_id' => $document->id,
                                    'client_matter_id' => $document->client_matter_id,
                                    'outstanding_required' => $outstandingRequired,
                                    'current_stage' => $currentStage->name,
                                ]);
                            } else {
                                $stages = WorkflowStageNavigation::orderedStages(
                                    $clientMatter->workflow_id ? (int) $clientMatter->workflow_id : null,
                                    $currentStage->workflow_id ? (int) $currentStage->workflow_id : null
                                );
                                $nextStage = WorkflowStageNavigation::nextAfter(
                                    $stages,
                                    (int) $clientMatter->workflow_stage_id
                                );

                                // Only auto-advance if next stage is NOT "Decision Received" (requires outcome/note)
                                if ($nextStage && strtolower(trim($nextStage->name ?? '')) !== 'decision received') {
                                    $fromStageName = (string) ($currentStage->name ?? '');
                                    $toStageName = (string) ($nextStage->name ?? '');
                                    $clientMatter->workflow_stage_id = $nextStage->id;
                                    $clientMatter->save();

                                    // applications table removed - workflow tracked via client_matters

                                    $matterNo = $clientMatter->client_unique_matter_no ?? 'ID: '.$clientMatter->id;
                                    $createdBy = (int) ($clientMatter->sel_person_responsible ?? 0);
                                    ActivitiesLog::create([
                                        'client_id' => $clientMatter->client_id,
                                        'created_by' => $createdBy > 0 ? $createdBy : null,
                                        'subject' => $matterNo.' Stage: '.$fromStageName,
                                        'description' => 'moved the stage from <b>'.$fromStageName.'</b> to <b>'.$toStageName.'</b>',
                                        'activity_type' => 'stage',
                                        'use_for' => 'matter',
                                        'task_status' => 0,
                                        'pin' => 0,
                                        'source' => 'document_sign',
                                    ]);

                                    Log::info('Workflow advanced on document sign', [
                                        'document_id' => $document->id,
                                        'client_matter_id' => $document->client_matter_id,
                                        'from_stage' => $fromStageName,
                                        'to_stage' => $toStageName,
                                    ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Workflow advancement on sign failed', [
                    'document_id' => $document->id,
                    'client_matter_id' => $document->client_matter_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
