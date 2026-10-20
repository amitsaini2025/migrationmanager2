<?php

namespace Tests\Unit;

use App\Support\CurrentVisaExpiry;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrentVisaExpiryTest extends TestCase
{
    #[Test]
    public function sheet_expiry_comes_from_the_latest_visa_row(): void
    {
        $sql = CurrentVisaExpiry::sql('admins');

        $this->assertStringContainsString('client_visa_countries cvc', $sql);
        $this->assertStringContainsString('cvc.client_id = admins.id', $sql);
        $this->assertStringContainsString('ORDER BY cvc.visa_expiry_date DESC', $sql);
        $this->assertStringNotContainsString('visaExpiry', $sql);
    }

    #[Test]
    public function sql_follows_the_admin_table_alias(): void
    {
        $sql = CurrentVisaExpiry::sql('a');

        $this->assertStringContainsString('cvc.client_id = a.id', $sql);
        $this->assertStringNotContainsString('visaExpiry', $sql);
    }

    #[Test]
    public function sql_rejects_an_unsafe_alias(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CurrentVisaExpiry::sql('admins; drop table admins');
    }

    #[Test]
    public function a_missing_client_has_no_display_expiry(): void
    {
        $this->assertSame('', CurrentVisaExpiry::formatForDisplay(null));
    }
}
