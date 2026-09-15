<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DashboardMyDayRoutesTest extends TestCase
{
    public function test_guest_cannot_hit_my_day_routes(): void
    {
        $this->getJson(route('dashboard.my-day'))->assertUnauthorized();
        $this->getJson(route('dashboard.my-day.matter-search', ['q' => 'test']))->assertUnauthorized();
        $this->getJson(route('dashboard.my-day.copy-summary'))->assertUnauthorized();
        $this->postJson(route('dashboard.my-day.file-time.start'), [
            'kind' => 'draft',
            'title' => 'x',
            'admin' => true,
        ])->assertUnauthorized();
    }

    public function test_copy_summary_route_is_registered(): void
    {
        $this->assertTrue(Route::has('dashboard.my-day.copy-summary'));
        $this->assertTrue(Route::has('dashboard.my-day.file-time.start'));
    }
}
