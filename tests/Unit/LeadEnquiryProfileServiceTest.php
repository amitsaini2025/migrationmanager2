<?php

namespace Tests\Unit;

use App\Services\LeadEnquiryProfileService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadEnquiryProfileServiceTest extends TestCase
{
    #[Test]
    public function detail_rules_include_migrate_enquiry_optional_fields(): void
    {
        $rules = LeadEnquiryProfileService::detailRules();

        foreach (['dob', 'marital_status', 'city', 'current_visa', 'visa_expiry', 'qualification', 'qualification_date', 'occupation'] as $key) {
            $this->assertArrayHasKey($key, $rules);
        }
    }

    #[Test]
    public function admin_attributes_map_city_and_dates(): void
    {
        $service = new LeadEnquiryProfileService;
        $attrs = $service->adminAttributesFromPayload([
            'dob' => '1990-05-01',
            'marital_status' => 'Single',
            'city' => 'Melbourne, VIC',
        ]);

        $this->assertSame('1990-05-01', $attrs['dob']);
        $this->assertSame('Single', $attrs['marital_status']);
        $this->assertSame('Melbourne, VIC', $attrs['city']);
        $this->assertSame('Melbourne, VIC', $attrs['address']);
        $this->assertSame('+61', $attrs['country_code']);
        $this->assertNull($attrs['country']);
    }
}
