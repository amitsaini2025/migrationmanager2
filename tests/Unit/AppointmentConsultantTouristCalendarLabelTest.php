<?php

namespace Tests\Unit;

use App\Models\AppointmentConsultant;
use App\Services\StaffPersonalCalendarFeedService;
use Tests\TestCase;

class AppointmentConsultantTouristCalendarLabelTest extends TestCase
{
    public function test_melbourne_tourist_calendar_displays_vijay(): void
    {
        $consultant = new AppointmentConsultant([
            'name' => 'Vijay',
            'calendar_type' => 'tourist',
            'location' => 'melbourne',
            'specializations' => [4],
            'is_active' => true,
            'show_in_filter' => true,
        ]);

        $this->assertSame('tourist', $consultant->calendar_type);
        $this->assertSame('Vijay', $consultant->calendar_type_display);
        $this->assertSame('Vijay', $consultant->crm_display_label);
        $this->assertSame(['Tourist Visa'], $consultant->getSpecializationNames());
    }

    public function test_melbourne_jrp_calendar_displays_shubham_without_yadwinder(): void
    {
        $consultant = new AppointmentConsultant([
            'name' => 'Shubham (JRP)',
            'calendar_type' => 'jrp',
            'location' => 'melbourne',
            'specializations' => [2, 3],
            'is_active' => true,
            'show_in_filter' => true,
        ]);

        $this->assertSame('jrp', $consultant->calendar_type);
        $this->assertSame('JRP/Skill Assessment', $consultant->calendar_type_display);
        $this->assertSame('Shubham (JRP)', $consultant->crm_display_label);
    }

    public function test_dashboard_calendar_options_label_tourist_as_vijay(): void
    {
        $service = new StaffPersonalCalendarFeedService;
        $labels = collect($service->calendarTypeOptions())->pluck('label', 'key');

        $this->assertSame('tourist', $service->normalizeCalendarType('tourist'));
        $this->assertSame('Vijay', $labels['tourist']);
    }
}
