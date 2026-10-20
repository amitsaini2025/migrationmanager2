<?php

namespace Tests\Unit;

use App\Support\CurrentVisaExpiry;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrentVisaExpiryTest extends TestCase
{
    #[Test]
    public function sheet_expiry_comes_from_the_latest_visa_row_and_falls_back_to_the_client_column(): void
    {
        $sql = CurrentVisaExpiry::sql('admins');

        $this->assertStringContainsString('client_visa_countries cvc', $sql);
        $this->assertStringContainsString('cvc.client_id = admins.id', $sql);
        $this->assertStringContainsString('ORDER BY cvc.visa_expiry_date DESC', $sql);
        $this->assertStringContainsString('ELSE admins."visaExpiry" END', $sql);
    }

    #[Test]
    public function sql_follows_the_admin_table_alias(): void
    {
        $sql = CurrentVisaExpiry::sql('a');

        $this->assertStringContainsString('cvc.client_id = a.id', $sql);
        $this->assertStringContainsString('ELSE a."visaExpiry" END', $sql);
    }

    #[Test]
    public function sql_rejects_an_unsafe_alias(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CurrentVisaExpiry::sql('admins; drop table admins');
    }
}
