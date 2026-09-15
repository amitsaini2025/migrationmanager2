<?php

namespace Tests\Unit\Services;

use App\Models\Staff;
use App\Services\StaffPersonalCalendarFeedService;
use PHPUnit\Framework\TestCase;

class StaffPersonalCalendarFeedServiceTest extends TestCase
{
    public function test_unknown_type_falls_back_to_employer_sponsored(): void
    {
        $service = new StaffPersonalCalendarFeedService;

        $this->assertSame('paid', $service->normalizeCalendarType(null));
        $this->assertSame('paid', $service->normalizeCalendarType('nope'));
        $this->assertSame('paid', StaffPersonalCalendarFeedService::DEFAULT_TYPE);
    }

    public function test_known_calendar_types_are_accepted(): void
    {
        $service = new StaffPersonalCalendarFeedService;

        $this->assertSame('paid', $service->normalizeCalendarType('paid'));
        $this->assertSame('jrp', $service->normalizeCalendarType('JRP'));
        $this->assertSame('ajay', $service->normalizeCalendarType('ajay'));
        $this->assertSame('arun', $service->normalizeCalendarType('arun'));
        $this->assertSame('tourist', $service->normalizeCalendarType('tourist'));
    }

    public function test_staff_email_selects_named_calendar(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $ajay = new Staff(['email' => 'ajay@bansalimmigration.com', 'first_name' => 'Ajay']);
        $vijay = new Staff(['email' => 'vijay@bansalimmigration.com', 'first_name' => 'Vijay']);
        $arun = new Staff(['email' => 'arun@bansalimmigration.com', 'first_name' => 'Arun']);

        $this->assertSame('ajay', $service->defaultTypeForStaff($ajay));
        $this->assertSame('tourist', $service->defaultTypeForStaff($vijay));
        $this->assertSame('paid', $service->defaultTypeForStaff($arun));
    }

    public function test_staff_first_name_selects_named_calendar_when_email_does_not_match(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $ajay = new Staff(['email' => 'office@bansalcrm.com', 'first_name' => 'Ajay']);

        $this->assertSame('ajay', $service->defaultTypeForStaff($ajay));
        $this->assertSame('paid', $service->defaultTypeForStaff(null));
        $this->assertSame('paid', $service->defaultTypeForStaff(new Staff(['email' => 'sam@bansalcrm.com', 'first_name' => 'Sam'])));
    }

    public function test_admin_console_default_calendar_is_used_when_set(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $sam = new Staff([
            'email' => 'sam@bansalcrm.com',
            'first_name' => 'Sam',
            'default_calendar_type' => 'adelaide',
        ]);
        $ajayOverride = new Staff([
            'email' => 'ajay@bansalimmigration.com',
            'first_name' => 'Ajay',
            'default_calendar_type' => 'education',
        ]);

        $this->assertSame('adelaide', $service->defaultTypeForStaff($sam));
        $this->assertSame('education', $service->defaultTypeForStaff($ajayOverride));
        $this->assertNull($service->optionalCalendarType(''));
        $this->assertNull($service->optionalCalendarType('nope'));
        $this->assertSame('jrp', $service->optionalCalendarType('JRP'));
    }
}
