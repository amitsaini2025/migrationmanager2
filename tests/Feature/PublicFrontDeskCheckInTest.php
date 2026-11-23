<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicFrontDeskCheckInTest extends TestCase
{
    public function test_public_office_check_in_is_a_separate_page_from_the_staff_wizard(): void
    {
        $this->assertTrue(Route::has('public.office-check-in.index'));
        $this->assertTrue(Route::has('public.office-check-in.lookup'));
        $this->assertTrue(Route::has('public.office-check-in.appointments'));
        $this->assertTrue(Route::has('public.office-check-in.submit'));
        $this->assertTrue(Route::has('public.office-check-in.create-lead'));
        $this->assertTrue(Route::has('front-desk.checkin.index'));

        $this->assertSame(url('/office-check-in'), route('public.office-check-in.index'));
        $this->assertSame(url('/front-desk/checkin'), route('front-desk.checkin.index'));
        $this->assertNotSame(route('public.office-check-in.index'), route('front-desk.checkin.index'));
    }

    public function test_a_visitor_can_open_the_public_office_check_in_page_without_logging_in(): void
    {
        $this->get(route('public.office-check-in.index'))
            ->assertOk()
            ->assertSee('Front-Desk Check-In')
            ->assertSee('No login needed')
            ->assertSee('Look Up')
            ->assertSee('>Refresh</button>', false)
            ->assertSee('id="refresh"', false);
    }

    public function test_the_existing_staff_check_in_page_still_requires_login(): void
    {
        $this->get('/front-desk/checkin')->assertRedirect(route('crm.login'));
    }

    public function test_public_lookup_asks_for_a_phone_number(): void
    {
        $this->postJson(route('public.office-check-in.lookup'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_legacy_check_in_lookup_alias_still_works(): void
    {
        $this->assertTrue(Route::has('public.check-in.lookup'));

        $this->postJson(route('public.check-in.lookup'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }
}
