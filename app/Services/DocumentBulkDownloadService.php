<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentBulkDownloadService
{
    public const ALLOWED_DOC_TYPES = ['personal', 'visa', 'nomination'];

    public const MAX_TOTAL_BYTES = 200 * 1024 * 1024;

    /**
     * @param  Collection<int, Document>  $documents
     * @return array{path: string, download_name: string, added_count: int, skipped_ids: list<int>}
     */
    public function buildZip(Admin $client, Collection $documents): array
    {
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $downloadName = $this->buildDownloadName($client);
        $zipPath = $tempDir.DIRECTORY_SEPARATOR.uniqid('bulk_docs_', true).'.zip';

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create ZIP file.');
        }

        $usedNames = [];
        $addedCount = 0;
        $totalBytes = 0;
        $skippedIds = [];
        $stagingPaths = [];

        try {
            foreach ($documents as $document) {
                try {
                    $location = OriginalDocumentPathResolver::locateOriginalPdfFile($document);
                } catch (\Throwable $e) {
                    $skippedIds[] = (int) $document->id;
                    Log::warning('Bulk download failed while locating document file', [
                        'document_id' => $document->id,
                        'client_id' => $client->id,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }

                if ($location === null) {
                    $skippedIds[] = (int) $document->id;
                    Log::warning('Bulk download skipped missing document file', [
                        'document_id' => $document->id,
                        'client_id' => $client->id,
                    ]);

                    continue;
                }

                $entryName = $this->uniqueZipEntryName(
                    $document->getOriginalDownloadFilename(),
                    $usedNames
                );

                if ($location['disk'] === 'local') {
                    $absolutePath = $location['path'];
                    $size = @filesize($absolutePath);
                    if ($size === false || $size <= 0) {
                        $skippedIds[] = (int) $document->id;
                        Log::warning('Bulk download skipped empty local file', [
                            'document_id' => $document->id,
                            'path' => $absolutePath,
                        ]);

                        continue;
                    }

                    if (($totalBytes + $size) > self::MAX_TOTAL_BYTES) {
                        $zip->close();
                        $this->cleanupStaging($stagingPaths);
                        @unlink($zipPath);
                        abort(422, 'Selected documents exceed the maximum download size.');
                    }

                    if (! $zip->addFile($absolutePath, $entryName)) {
                        $skippedIds[] = (int) $document->id;
                        Log::warning('Bulk download failed to add local file to ZIP', [
                            'document_id' => $document->id,
                        ]);

                        continue;
                    }

                    $totalBytes += $size;
                    $addedCount++;

                    continue;
                }

                try {
                    $size = Storage::disk('s3')->size($location['key']);
                } catch (\Throwable $e) {
                    $skippedIds[] = (int) $document->id;
                    Log::warning('Bulk download failed to read S3 file size', [
                        'document_id' => $document->id,
                        's3_key' => $location['key'],
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }

                if ($size <= 0) {
                    $skippedIds[] = (int) $document->id;

                    continue;
                }

                if (($totalBytes + $size) > self::MAX_TOTAL_BYTES) {
                    $zip->close();
                    $this->cleanupStaging($stagingPaths);
                    @unlink($zipPath);
                    abort(422, 'Selected documents exceed the maximum download size.');
                }

                try {
                    $content = Storage::disk('s3')->get($location['key']);
                } catch (\Throwable $e) {
                    $skippedIds[] = (int) $document->id;
                    Log::warning('Bulk download failed to read S3 file', [
                        'document_id' => $document->id,
                        's3_key' => $location['key'],
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }

                $stagingPath = $tempDir.DIRECTORY_SEPARATOR.uniqid('bulk_doc_part_', true);
                if (file_put_contents($stagingPath, $content) === false) {
                    $skippedIds[] = (int) $document->id;
                    Log::warning('Bulk download failed to stage S3 file', [
                        'document_id' => $document->id,
                    ]);

                    continue;
                }

                $stagingPaths[] = $stagingPath;

                if (! $zip->addFile($stagingPath, $entryName)) {
                    $skippedIds[] = (int) $document->id;
                    Log::warning('Bulk download failed to add staged file to ZIP', [
                        'document_id' => $document->id,
                    ]);

                    continue;
                }

                $totalBytes += $size;
                $addedCount++;
            }

            $zip->close();
        } catch (\Throwable $e) {
            try {
                $zip->close();
            } catch (\Throwable) {
                // ignore
            }
            $this->cleanupStaging($stagingPaths);
            @unlink($zipPath);
            throw $e;
        }

        $this->cleanupStaging($stagingPaths);

        if ($addedCount === 0) {
            @unlink($zipPath);
            abort(404, 'No downloadable files found for the selected documents.');
        }

        return [
            'path' => $zipPath,
            'download_name' => $downloadName,
            'added_count' => $addedCount,
            'skipped_ids' => $skippedIds,
        ];
    }

    public function documentHasStoredFile(Document $document): bool
    {
        if (trim((string) ($document->file_name ?? '')) === '') {
            return false;
        }

        return trim((string) ($document->myfile ?? '')) !== ''
            || trim((string) ($document->myfile_key ?? '')) !== '';
    }

    private function buildDownloadName(Admin $client): string
    {
        $raw = trim((string) ($client->company_name_or_personal_name ?: 'client'));
        $safe = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $raw) ?: 'client';
        $safe = trim($safe, '_');
        if ($safe === '') {
            $safe = 'client';
        }

        return $safe.'_documents_'.now()->format('Ymd_His').'.zip';
    }

    /**
     * @param  array<string, true>  $usedNames
     */
    private function uniqueZipEntryName(string $filename, array &$usedNames): string
    {
        $filename = str_replace(['\\', '/', "\0"], '_', $filename);
        $filename = trim($filename);
        if ($filename === '' || $filename === '.' || $filename === '..') {
            $filename = 'document.pdf';
        }

        if (! isset($usedNames[$filename])) {
            $usedNames[$filename] = true;

            return $filename;
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $i = 2;
        do {
            $candidate = $ext !== ''
                ? $base.'_'.$i.'.'.$ext
                : $base.'_'.$i;
            $i++;
        } while (isset($usedNames[$candidate]));

        $usedNames[$candidate] = true;

        return $candidate;
    }

    /**
     * @param  list<string>  $paths
     */
    private function cleanupStaging(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path) && $path !== '' && is_file($path)) {
                @unlink($path);
            }
        }
    }
}
