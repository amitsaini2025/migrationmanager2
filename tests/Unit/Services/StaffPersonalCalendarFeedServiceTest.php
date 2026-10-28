<?php

namespace Tests\Unit\Services;

use App\Models\Staff;
use App\Services\StaffPersonalCalendarFeedService;
use PHPUnit\Framework\TestCase;

class StaffPersonalCalendarFeedServiceTest extends TestCase
{
    public function test_unknown_type_falls_back_to_jrp(): void
    {
        $service = new StaffPersonalCalendarFeedService;

        $this->assertSame('jrp', $service->normalizeCalendarType(null));
        $this->assertSame('jrp', $service->normalizeCalendarType('nope'));
        $this->assertSame('jrp', StaffPersonalCalendarFeedService::DEFAULT_TYPE);
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

    public function test_retired_paid_type_resolves_to_jrp_for_ui(): void
    {
        $service = new StaffPersonalCalendarFeedService;

        $this->assertSame('jrp', $service->resolveForUi('paid'));
        $this->assertSame('jrp', $service->resolveForUi(null));
        $this->assertSame('education', $service->resolveForUi('education'));
        $this->assertNull($service->optionalCalendarType('paid'));
    }

    public function test_staff_email_selects_named_calendar(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $ajay = new Staff(['email' => 'ajay@bansalimmigration.com', 'first_name' => 'Ajay']);
        $vijay = new Staff(['email' => 'vijay@bansalimmigration.com', 'first_name' => 'Vijay']);
        $arun = new Staff(['email' => 'arun@bansalimmigration.com', 'first_name' => 'Arun']);

        $this->assertSame('ajay', $service->defaultTypeForStaff($ajay));
        $this->assertSame('tourist', $service->defaultTypeForStaff($vijay));
        $this->assertSame('jrp', $service->defaultTypeForStaff($arun));
    }

    public function test_staff_first_name_selects_named_calendar_when_email_does_not_match(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $ajay = new Staff(['email' => 'office@bansalcrm.com', 'first_name' => 'Ajay']);

        $this->assertSame('ajay', $service->defaultTypeForStaff($ajay));
        $this->assertSame('jrp', $service->defaultTypeForStaff(null));
        $this->assertSame('jrp', $service->defaultTypeForStaff(new Staff(['email' => 'sam@bansalcrm.com', 'first_name' => 'Sam'])));
    }

    public function test_staff_with_retired_paid_default_falls_back_to_jrp(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $staff = new Staff([
            'email' => 'sam@bansalcrm.com',
            'first_name' => 'Sam',
            'default_calendar_type' => 'paid',
        ]);

        $this->assertSame('jrp', $service->defaultTypeForStaff($staff));
    }

    public function test_dashboard_and_booking_calendar_options_exclude_retired_paid_calendar(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $bookingKeys = collect($service->bookingCalendarTypeOptions())->pluck('key')->all();
        $dashboardKeys = collect($service->calendarTypeOptions())->pluck('key')->all();

        $this->assertNotContains('paid', $bookingKeys);
        $this->assertNotContains('paid', $dashboardKeys);
        $this->assertContains('jrp', $bookingKeys);
        $this->assertContains('jrp', $dashboardKeys);
        $this->assertSame($bookingKeys, $dashboardKeys);
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
