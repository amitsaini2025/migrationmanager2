<?php

namespace App\Support;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Uploaded receipt/invoice document links for the accounts lists.
 */
class AccountReceiptDocumentUrls
{
    /**
     * @param  iterable<int, object>  $rows
     */
    public static function attach(iterable $rows): void
    {
        $docIds = [];
        $matterIds = [];
        $clientIds = [];
        foreach ($rows as $row) {
            $docId = (int) ($row->uploaded_doc_id ?? 0);
            if ($docId > 0) {
                $docIds[] = $docId;
            }
            $matterId = (int) ($row->client_matter_id ?? 0);
            if ($matterId > 0) {
                $matterIds[] = $matterId;
            }
            $clientId = (int) ($row->client_id ?? 0);
            if ($clientId > 0) {
                $clientIds[] = $clientId;
            }
        }

        $documents = collect();
        if ($docIds !== []) {
            $documents = DB::table('documents')
                ->whereIn('id', array_values(array_unique($docIds)))
                ->get()
                ->keyBy('id');
        }

        $matters = collect();
        if ($matterIds !== []) {
            $matters = DB::table('client_matters')
                ->select('id', 'client_unique_matter_no')
                ->whereIn('id', array_values(array_unique($matterIds)))
                ->get()
                ->keyBy('id');
        }

        $clientRefs = collect();
        if ($clientIds !== []) {
            $clientRefs = DB::table('admins')
                ->whereIn('id', array_values(array_unique($clientIds)))
                ->pluck('client_id', 'id');
        }

        foreach ($rows as $row) {
            $row->account_dropdown_doc_url = self::urlForRow($row, $documents, $matters, $clientRefs);
        }
    }

    /**
     * @param  Collection<int|string, object>  $documents
     * @param  Collection<int|string, object>  $matters
     * @param  Collection<int|string, mixed>  $clientRefs
     */
    private static function urlForRow(object $row, $documents, $matters, $clientRefs): ?string
    {
        $docId = (int) ($row->uploaded_doc_id ?? 0);
        $document = $docId > 0 ? $documents->get($docId) : null;
        if (! $document || empty($document->myfile)) {
            return null;
        }

        if (filter_var($document->myfile, FILTER_VALIDATE_URL)) {
            return (string) $document->myfile;
        }

        $clientRef = (string) ($clientRefs->get((int) ($row->client_id ?? 0)) ?? '');
        if ($clientRef === '') {
            return null;
        }

        $matter = $matters->get((int) ($row->client_matter_id ?? 0));
        $matterRef = $matter->client_unique_matter_no ?? null;
        $filePath = $matterRef
            ? $clientRef.'/'.$matterRef.'/accounts/'.$document->myfile
            : $clientRef.'/accounts/'.$document->myfile;

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        return $disk->url($filePath);
    }
}
