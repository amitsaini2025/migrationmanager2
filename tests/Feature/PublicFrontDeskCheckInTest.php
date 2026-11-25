<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
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

    public function test_public_lookup_rejects_non_australian_phone_numbers(): void
    {
        $this->postJson(route('public.office-check-in.lookup'), [
            'phone' => '12345',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);

        $this->postJson(route('public.office-check-in.lookup'), [
            'phone' => '+44 7700 900123',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_public_lookup_accepts_australian_numbers_with_or_without_leading_zero(): void
    {
        $this->createLookupSchema();

        Admin::query()->create([
            'type' => 'lead',
            'first_name' => 'Sam',
            'last_name' => 'Visitor',
            'email' => 'sam.visitor@example.com',
            'phone' => '0406660960',
            'is_archived' => null,
            'is_deleted' => null,
        ]);

        foreach (['0406660960', '406660960', '+61406660960'] as $phone) {
            $this->postJson(route('public.office-check-in.lookup'), [
                'phone' => $phone,
            ])
                ->assertOk()
                ->assertJsonPath('matches.0.phone', '0406660960')
                ->assertJsonPath('matches.0.name', 'Sam Visitor');
        }
    }

    private function createLookupSchema(): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('type')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_company')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedInteger('is_archived')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('company_name')->nullable();
                $table->timestamps();
            });
        }
    }
}
