<?php

namespace Tests\Feature;

use App\Http\Controllers\CRM\FrontDeskCheckInController;
use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use ReflectionMethod;
use Tests\TestCase;

class FrontDeskCheckInAccessTest extends TestCase
{
    protected Staff $restrictedStaff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();

        config([
            'crm.front_desk_checkin_role_ids' => [1, 12, 14, 17],
            'crm_access.exempt_role_ids' => [1, 17],
            'crm_access.exempt_staff_ids' => [],
        ]);

        $this->restrictedStaff = Staff::query()->create([
            'first_name' => 'Any',
            'last_name' => 'Role',
            'email' => 'front-desk-any-role@test.com',
            'password' => Hash::make('password'),
            'role' => 13,
            'status' => 1,
        ]);
    }

    public function test_restricted_role_still_fails_legacy_front_desk_role_helper(): void
    {
        $this->assertFalse($this->restrictedStaff->canAccessFrontDeskCheckIn());
    }

    public function test_header_shortcut_still_uses_role_helper(): void
    {
        $header = file_get_contents(resource_path('views/Elements/CRM/header_client_detail.blade.php'));

        $this->assertNotFalse($header);
        $this->assertStringContainsString('canAccessFrontDeskCheckIn()', $header);
    }

    public function test_any_authenticated_staff_passes_front_desk_controller_authorisation(): void
    {
        $this->actingAs($this->restrictedStaff, 'admin');

        $method = new ReflectionMethod(FrontDeskCheckInController::class, 'authorised');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke(app(FrontDeskCheckInController::class)));
    }

    public function test_any_authenticated_staff_can_open_front_desk_checkin_page(): void
    {
        $this->actingAs($this->restrictedStaff, 'admin');

        $result = app(FrontDeskCheckInController::class)->index();

        $this->assertInstanceOf(View::class, $result);
        $this->assertSame('crm.front_desk.checkin', $result->name());
    }

    public function test_controller_authorisation_does_not_call_role_helper(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/CRM/FrontDeskCheckInController.php'));

        $this->assertNotFalse($controller);
        $this->assertStringNotContainsString('canAccessFrontDeskCheckIn()', $controller);
        $this->assertStringContainsString('instanceof Staff', $controller);
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->unsignedInteger('role')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->timestamps();
            });
        }

    }
}
