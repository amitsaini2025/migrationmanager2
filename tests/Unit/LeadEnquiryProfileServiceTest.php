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

        foreach (['dob', 'marital_status', 'gender', 'address', 'city', 'current_visa', 'visa_expiry', 'qualification', 'qualification_type', 'qualification_date', 'occupation', 'has_job_experience', 'job_experience_years', 'ielts_passed', 'ielts_score', 'pte_passed', 'pte_score', 'source'] as $key) {
            $this->assertArrayHasKey($key, $rules);
        }

        $this->assertContains('required', $rules['dob']);
        $this->assertContains('required', $rules['marital_status']);
        $this->assertContains('required', $rules['gender']);
        $this->assertContains('required', $rules['source']);
        $this->assertContains('required_if:has_job_experience,yes', $rules['job_experience_years']);
        $this->assertContains('required_if:ielts_passed,yes', $rules['ielts_score']);
        $this->assertContains('required_if:pte_passed,yes', $rules['pte_score']);
        $this->assertContains('Google', LeadEnquiryProfileService::heardAboutOptions());
        $this->assertContains('Walk-In', LeadEnquiryProfileService::heardAboutOptions());
    }

    #[Test]
    public function admin_attributes_map_city_and_dates(): void
    {
        $service = new LeadEnquiryProfileService;
        $attrs = $service->adminAttributesFromPayload([
            'dob' => '1990-05-01',
            'marital_status' => 'Single',
            'gender' => 'Female',
            'address' => '12 Example St',
            'city' => 'Melbourne, VIC',
            'source' => 'Google',
        ]);

        $this->assertSame('1990-05-01', $attrs['dob']);
        $this->assertSame('Single', $attrs['marital_status']);
        $this->assertSame('Female', $attrs['gender']);
        $this->assertSame('12 Example St', $attrs['address']);
        $this->assertSame('Melbourne, VIC', $attrs['city']);
        $this->assertSame('Google', $attrs['source']);
        $this->assertSame('+61', $attrs['country_code']);
        $this->assertNull($attrs['country']);
    }

    #[Test]
    public function admin_attributes_fall_back_address_to_city_when_address_omitted(): void
    {
        $service = new LeadEnquiryProfileService;
        $attrs = $service->adminAttributesFromPayload([
            'city' => 'Melbourne, VIC',
        ]);

        $this->assertSame('Melbourne, VIC', $attrs['city']);
        $this->assertSame('Melbourne, VIC', $attrs['address']);
    }
}
