<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\VisaTypeSheetController;
use App\Models\ActivitiesLog;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisaSheetVerificationActivityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('activities_logs');
        Schema::dropIfExists('client_matters');
        Schema::dropIfExists('matters');
        Schema::dropIfExists('staff');

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

        Schema::create('matters', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
        });

        Schema::create('client_matters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('sel_matter_id')->nullable();
            $table->string('client_unique_matter_no')->nullable();
        });

        Schema::create('activities_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('activity_type')->nullable();
            $table->unsignedTinyInteger('task_status')->default(0);
            $table->unsignedTinyInteger('pin')->default(0);
            $table->timestamps();
        });
    }

    #[Test]
    public function verifying_a_file_records_the_staff_member_on_the_activity_feed(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada-verify@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
        Auth::guard('admin')->login($staff);

        $matterId = DB::table('matters')->insertGetId([
            'title' => '482 - Skills in Demand',
        ]);
        $clientMatterId = DB::table('client_matters')->insertGetId([
            'client_id' => 42,
            'sel_matter_id' => $matterId,
            'client_unique_matter_no' => 'JASP2506035-482_1',
        ]);

        $controller = new class extends VisaTypeSheetController
        {
            public function record(int $clientId, int $matterId, bool $verified): void
            {
                $this->recordSheetVerificationActivity($clientId, $matterId, $verified);
            }
        };

        $controller->record(42, (int) $clientMatterId, true);

        $activity = ActivitiesLog::query()->first();
        $this->assertNotNull($activity);
        $this->assertSame(42, (int) $activity->client_id);
        $this->assertSame((int) $staff->id, (int) $activity->created_by);
        $this->assertSame('verified the file - JASP2506035-482_1', $activity->subject);
        $this->assertStringContainsString('Ada Lovelace', (string) $activity->description);
        $this->assertStringContainsString('482 - Skills in Demand', (string) $activity->description);
        $this->assertSame('activity', $activity->activity_type);
    }

    #[Test]
    public function verified_star_tooltip_includes_the_staff_name(): void
    {
        $controller = new class extends VisaTypeSheetController
        {
            public function title(bool $verified, ?string $name): string
            {
                return $this->verifiedStarTitle($verified, $name);
            }
        };

        $this->assertSame('Verified by Ada Lovelace', $controller->title(true, 'Ada Lovelace'));
        $this->assertSame('Verified', $controller->title(true, '  '));
        $this->assertSame('Mark as verified', $controller->title(false, 'Ada Lovelace'));
    }
}
