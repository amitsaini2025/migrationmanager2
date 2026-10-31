<?php

namespace Tests\Unit;

use App\Support\EmailAttachmentFilename;
use Tests\TestCase;

class EmailAttachmentFilenameTest extends TestCase
{
    public function test_normalize_key_trims_and_lowercases(): void
    {
        $this->assertSame('loveleen_pte.pdf', EmailAttachmentFilename::normalizeKey('  Loveleen_PTE.PDF  '));
    }

    public function test_normalize_key_strips_windows_and_unix_path_prefixes(): void
    {
        $this->assertSame(
            'passport.pdf',
            EmailAttachmentFilename::normalizeKey('C:\\Users\\me\\Documents\\Passport.pdf')
        );
        $this->assertSame(
            'passport.pdf',
            EmailAttachmentFilename::normalizeKey('/tmp/uploads/Passport.pdf')
        );
    }

    public function test_normalize_key_matches_preview_and_parse_variants(): void
    {
        $preview = 'Documents_Loveleen Singh_image001.png';
        $parsed = 'documents_loveleen singh_image001.png';

        $this->assertSame(
            EmailAttachmentFilename::normalizeKey($preview),
            EmailAttachmentFilename::normalizeKey($parsed)
        );
    }

    public function test_normalize_key_returns_empty_for_blank_values(): void
    {
        $this->assertSame('', EmailAttachmentFilename::normalizeKey(null));
        $this->assertSame('', EmailAttachmentFilename::normalizeKey(''));
        $this->assertSame('', EmailAttachmentFilename::normalizeKey('   '));
    }
}
