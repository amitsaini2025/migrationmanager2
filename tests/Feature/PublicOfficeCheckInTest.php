<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicOfficeCheckInTest extends TestCase
{
    public function test_check_in_chooser_and_office_wizard_routes_are_separate(): void
    {
        $this->assertTrue(Route::has('public.check-in.index'));
        $this->assertTrue(Route::has('public.office-check-in.index'));
        $this->assertTrue(Route::has('public.office-check-in.lookup'));
        $this->assertTrue(Route::has('public.office-check-in.appointments'));
        $this->assertTrue(Route::has('public.office-check-in.submit'));
        $this->assertTrue(Route::has('public.office-check-in.create-lead'));

        $this->assertSame(url('/check-in'), route('public.check-in.index'));
        $this->assertSame(url('/office-check-in'), route('public.office-check-in.index'));
        $this->assertNotSame(route('public.check-in.index'), route('public.office-check-in.index'));
    }

    public function test_a_visitor_can_open_the_check_in_chooser_without_logging_in(): void
    {
        $this->get(route('public.check-in.index'))
            ->assertOk()
            ->assertSee('Welcome to Bansal Immigration and Education')
            ->assertSee('Immigration')
            ->assertSee('Education')
            ->assertSee('href="'.e(route('public.office-check-in.index')).'"', false)
            ->assertSee('href="'.e(config('services.education_check_in.url')).'"', false);
    }

    public function test_immigration_option_opens_the_office_check_in_wizard(): void
    {
        $this->get(route('public.office-check-in.index'))
            ->assertOk()
            ->assertSee('Front-Desk Check-In')
            ->assertSee('Look Up')
            ->assertDontSee('Welcome to Bansal Immigration and Education');
    }
}
