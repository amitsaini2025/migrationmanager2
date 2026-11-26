<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Services\FrontDesk\CheckInLookupService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckInLookupServiceTest extends TestCase
{
    #[Test]
    public function lookup_uses_active_crm_record_constraints(): void
    {
        $source = file_get_contents(app_path('Services/FrontDesk/CheckInLookupService.php'));

        $this->assertNotFalse($source);
        $this->assertStringContainsString('constrainToActiveCrmRecords', $source);
        $this->assertStringNotContainsString("->whereNull('is_deleted')", $source);
    }

    #[Test]
    public function active_crm_constraint_excludes_deleted_and_archived(): void
    {
        $sql = Admin::query()
            ->tap(static fn ($query) => Admin::constrainToActiveCrmRecords($query))
            ->toSql();

        $this->assertStringContainsString('is_deleted', $sql);
        $this->assertStringContainsString('is_archived', $sql);
    }

    #[Test]
    public function normalize_phone_uses_australian_national_form_when_possible(): void
    {
        $service = new CheckInLookupService;

        $this->assertSame('0412345678', $service->normalizePhone('0412 345-678'));
        $this->assertSame('0412345678', $service->normalizePhone('+61 412 345 678'));
        $this->assertSame('0412345678', $service->normalizePhone('412345678'));
    }

    #[Test]
    public function canonical_australian_phone_accepts_with_or_without_leading_zero(): void
    {
        $service = new CheckInLookupService;

        $this->assertSame('0406660960', $service->canonicalAustralianPhone('0406660960'));
        $this->assertSame('0406660960', $service->canonicalAustralianPhone('406660960'));
        $this->assertSame('0406660960', $service->canonicalAustralianPhone('+61 406 660 960'));
        $this->assertSame('0406660960', $service->canonicalAustralianPhone('61406660960'));
        $this->assertSame('', $service->canonicalAustralianPhone('12345'));
        $this->assertSame('', $service->canonicalAustralianPhone('+44 7700 900123'));
    }

    #[Test]
    public function phone_digit_match_variants_cover_zero_and_country_code_forms(): void
    {
        $expected = ['0406660960', '406660960', '61406660960'];

        $withZero = Admin::phoneDigitMatchVariants('0406660960');
        $withoutZero = Admin::phoneDigitMatchVariants('406660960');
        $withCountryCode = Admin::phoneDigitMatchVariants('61406660960');

        sort($expected, SORT_STRING);
        sort($withZero, SORT_STRING);
        sort($withoutZero, SORT_STRING);
        sort($withCountryCode, SORT_STRING);

        $this->assertSame($expected, $withZero);
        $this->assertSame($expected, $withoutZero);
        $this->assertSame($expected, $withCountryCode);
    }
}
