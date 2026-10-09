<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\ClientMatter;
use App\Models\Document;
use App\Models\NominationDocumentType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores LMT advertisement copies in the matter's nomination LMT folder.
 */
final class LmtAdvertisementFiles
{
    public const FOLDER_TITLE = 'LMT';

    public const MAX_BYTES = 20 * 1024 * 1024;

    public function ensureFolder(int $clientId, int $matterId): NominationDocumentType
    {
        $folder = NominationDocumentType::query()
            ->where('client_id', $clientId)
            ->where('client_matter_id', $matterId)
            ->whereRaw('LOWER(TRIM(title)) = ?', [strtolower(self::FOLDER_TITLE)])
            ->first();

        if ($folder) {
            if ((int) $folder->status !== 1) {
                $folder->status = 1;
                $folder->save();
            }

            return $folder;
        }

        return NominationDocumentType::query()->create([
            'title' => self::FOLDER_TITLE,
            'status' => 1,
            'client_id' => $clientId,
            'client_matter_id' => $matterId,
        ]);
    }

    /**
     * @param  list<mixed>  $files
     * @return list<UploadedFile>
     */
    public function acceptedUploads(array $files): array
    {
        return array_values(array_filter(
            $files,
            fn ($file) => $file instanceof UploadedFile && $file->isValid()
        ));
    }

    /**
     * @param  list<mixed>  $files
     * @return list<string>
     */
    public function validateFiles(array $files): array
    {
        $errors = [];
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $fileName = $file->getClientOriginalName() ?: 'A file';
            if (! $file->isValid()) {
                $errors[] = $fileName.' did not upload. Please try again.';

                continue;
            }
            if (! DocumentFilenameRules::isAllowed($fileName)) {
                $errors[] = $fileName.': '.DocumentFilenameRules::validationMessage();
            }
            if ($file->getSize() > self::MAX_BYTES) {
                $errors[] = $fileName.' exceeds the 20MB limit.';
            }
        }

        return $errors;
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    public function store(Admin $client, ClientMatter $matter, array $files, int $userId): void
    {
        $uploads = $this->acceptedUploads($files);
        if ($uploads === []) {
            return;
        }

        $clientUniqueId = trim((string) ($client->client_id ?? ''));
        if ($clientUniqueId === '') {
            throw new \InvalidArgumentException('This company has no client reference, so advertisement files cannot be stored.');
        }

        $folder = $this->ensureFolder((int) $client->id, (int) $matter->id);
        $prefix = DocumentStoredFilename::storedNamePrefix($client, 'company');

        foreach ($uploads as $index => $file) {
            $extension = $file->getClientOriginalExtension();
            $checklist = $this->checklistName($file->getClientOriginalName());
            $uniqueId = time().'_'.$index.'_'.Str::lower(Str::random(4));
            $storedKey = $prefix.'_'.$checklist.'_'.$uniqueId.($extension !== '' ? '.'.$extension : '');
            $filePath = $clientUniqueId.'/nomination/'.$storedKey;

            Storage::disk('s3')->put($filePath, $file->getContent());

            $document = new Document;
            $document->user_id = $userId;
            $document->client_id = $client->id;
            $document->client_matter_id = $matter->id;
            $document->type = 'client';
            $document->doc_type = 'nomination';
            $document->folder_name = (string) $folder->id;
            $document->checklist = $checklist;
            $document->file_name = $prefix.'_'.$checklist.'_'.$uniqueId;
            $document->filetype = $extension;
            $document->myfile = Storage::disk('s3')->url($filePath);
            $document->myfile_key = $storedKey;
            $document->file_size = $file->getSize();
            $document->save();
        }
    }

    /**
     * @param  list<int>  $matterIds
     * @return array<int, list<array{name: string, url: ?string}>>
     */
    public function filesForMatters(array $matterIds): array
    {
        $matterIds = array_values(array_filter(array_map('intval', $matterIds)));
        if ($matterIds === []) {
            return [];
        }

        $folders = NominationDocumentType::query()
            ->whereIn('client_matter_id', $matterIds)
            ->whereRaw('LOWER(TRIM(title)) = ?', [strtolower(self::FOLDER_TITLE)])
            ->get(['id', 'client_matter_id']);

        if ($folders->isEmpty()) {
            return [];
        }

        $folderIds = $folders->pluck('id')->map(fn ($id) => (string) $id)->all();
        $matterByFolder = [];
        foreach ($folders as $folder) {
            $matterByFolder[(string) $folder->id] = (int) $folder->client_matter_id;
        }

        $documents = Document::query()
            ->where('doc_type', 'nomination')
            ->where('type', 'client')
            ->whereIn('folder_name', $folderIds)
            ->whereIn('client_matter_id', $matterIds)
            ->whereNull('not_used_doc')
            ->whereNotNull('myfile_key')
            ->where('myfile_key', '!=', '')
            ->orderBy('id')
            ->get(['id', 'client_matter_id', 'folder_name', 'checklist', 'file_name', 'myfile']);

        $grouped = [];
        foreach ($documents as $document) {
            $matterId = (int) $document->client_matter_id;
            $folderMatterId = $matterByFolder[(string) $document->folder_name] ?? null;
            if ($folderMatterId !== $matterId) {
                continue;
            }

            $name = trim((string) ($document->checklist ?: $document->file_name));
            $grouped[$matterId][] = [
                'name' => $name !== '' ? $name : 'Advertisement',
                'url' => $document->myfile ?: null,
            ];
        }

        return $grouped;
    }

    private function checklistName(string $originalName): string
    {
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        $clean = trim((string) preg_replace('/[^a-zA-Z0-9_\-\.\s\$\(\),&+\']+/', '', $base));
        if ($clean === '') {
            return 'Advertisement';
        }

        return Str::limit($clean, 80, '');
    }
}
