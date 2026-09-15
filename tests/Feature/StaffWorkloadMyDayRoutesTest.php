<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class StaffWorkloadMyDayRoutesTest extends TestCase
{
    public function test_guest_cannot_open_staff_my_day_summary(): void
    {
        $this->getJson(route('adminconsole.staff.workload.my-day', ['staff' => 1]))
            ->assertUnauthorized();
    }

    public function test_workload_my_day_route_is_registered(): void
    {
        $this->assertTrue(Route::has('adminconsole.staff.workload'));
        $this->assertTrue(Route::has('adminconsole.staff.workload.my-day'));
    }
}
