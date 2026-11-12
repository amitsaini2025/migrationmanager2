<?php

namespace Tests\Unit;

use App\Support\EmailAttachmentStorageMap;
use Tests\TestCase;

class EmailAttachmentStorageMapTest extends TestCase
{
    public function test_from_list_indexes_exact_and_normalized_keys(): void
    {
        $map = EmailAttachmentStorageMap::fromList([
            [
                'original_filename' => 'Loveleen_PTE.PDF',
                'storage_type' => 'documents',
                'category_id' => 12,
                'doc_type' => 'visa',
            ],
        ]);

        $this->assertArrayHasKey('Loveleen_PTE.PDF', $map);
        $this->assertArrayHasKey('loveleen_pte.pdf', $map);
        $this->assertSame('visa', $map['loveleen_pte.pdf']['doc_type']);
    }

    public function test_resolve_matches_preview_and_parse_filename_variants(): void
    {
        $map = EmailAttachmentStorageMap::fromList([
            [
                'original_filename' => 'Documents_Loveleen Singh_image001.png',
                'storage_type' => 'documents',
                'category_id' => 5,
                'doc_type' => 'visa',
                'file_name' => 'image001',
            ],
        ]);

        $resolved = EmailAttachmentStorageMap::resolve(
            $map,
            'documents_loveleen singh_image001.png'
        );

        $this->assertNotNull($resolved);
        $this->assertSame('documents', $resolved['storage_type']);
        $this->assertSame(5, $resolved['category_id']);
        $this->assertSame('visa', $resolved['doc_type']);
    }

    public function test_resolve_returns_null_when_filename_not_in_map(): void
    {
        $map = EmailAttachmentStorageMap::fromList([
            [
                'original_filename' => 'kept.pdf',
                'storage_type' => 'documents',
                'category_id' => 1,
                'doc_type' => 'personal',
            ],
        ]);

        $this->assertNull(EmailAttachmentStorageMap::resolve($map, 'removed.pdf'));
    }

    public function test_requests_document_copy_requires_documents_type_and_category(): void
    {
        $this->assertTrue(EmailAttachmentStorageMap::requestsDocumentCopy([
            'storage_type' => 'documents',
            'category_id' => 9,
        ]));
        $this->assertFalse(EmailAttachmentStorageMap::requestsDocumentCopy([
            'storage_type' => 'email',
            'category_id' => 9,
        ]));
        $this->assertFalse(EmailAttachmentStorageMap::requestsDocumentCopy([
            'storage_type' => 'documents',
            'category_id' => 0,
        ]));
    }

    public function test_from_json_handles_invalid_and_empty_payloads(): void
    {
        $this->assertSame([], EmailAttachmentStorageMap::fromJson(null));
        $this->assertSame([], EmailAttachmentStorageMap::fromJson(''));
        $this->assertSame([], EmailAttachmentStorageMap::fromJson('not-json'));
        $this->assertSame([], EmailAttachmentStorageMap::fromJson('[]'));
    }

    public function test_is_reviewed_list_ignores_empty_map(): void
    {
        $this->assertFalse(EmailAttachmentStorageMap::isReviewedList(true, []));
        $this->assertFalse(EmailAttachmentStorageMap::isReviewedList(false, []));
        $this->assertTrue(EmailAttachmentStorageMap::isReviewedList(true, [
            'kept.pdf' => [
                'original_filename' => 'kept.pdf',
                'storage_type' => 'documents',
                'category_id' => 1,
            ],
        ]));
    }

    public function test_from_list_sanitizes_nul_suffixed_keys_for_matching(): void
    {
        $map = EmailAttachmentStorageMap::fromList([
            [
                'original_filename' => "image001.png\0",
                'storage_type' => 'documents',
                'category_id' => 1,
                'doc_type' => 'visa',
            ],
        ]);

        $resolved = EmailAttachmentStorageMap::resolve($map, "image001.png\0");
        $this->assertNotNull($resolved);
        $this->assertSame('image001.png', $resolved['original_filename']);
        $this->assertSame(1, $resolved['category_id']);

        $resolvedClean = EmailAttachmentStorageMap::resolve($map, 'image001.png');
        $this->assertNotNull($resolvedClean);
    }

    public function test_remove_all_sentinel_marks_list_as_reviewed(): void
    {
        $map = EmailAttachmentStorageMap::fromList([
            [
                'original_filename' => '__removed_all__',
                'storage_type' => 'skip',
            ],
        ]);

        $this->assertTrue(EmailAttachmentStorageMap::isReviewedList(true, $map));
        $this->assertNull(EmailAttachmentStorageMap::resolve($map, 'image001.png'));
    }
}
