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
    public function normalize_phone_strips_non_digits(): void
    {
        $service = new CheckInLookupService;

        $this->assertSame('0412345678', $service->normalizePhone('0412 345-678'));
        $this->assertSame('61412345678', $service->normalizePhone('+61 412 345 678'));
    }
}
