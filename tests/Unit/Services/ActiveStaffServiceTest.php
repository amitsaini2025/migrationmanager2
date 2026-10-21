<?php

namespace Tests\Unit\Services;

use App\Models\Staff;
use App\Services\ActiveStaffService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActiveStaffServiceTest extends TestCase
{
    private ActiveStaffService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        $this->createSchema();
        $this->service = app(ActiveStaffService::class);
    }

    #[Test]
    public function returns_empty_list_when_no_recent_sessions(): void
    {
        $result = $this->service->getActiveStaff(5);

        $this->assertSame([], $result['data']->all());
        $this->assertSame(0, $result['meta']['total']);
    }

    #[Test]
    public function caches_active_staff_rows_as_plain_arrays(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-21 12:00:00'));

        Staff::query()->create([
            'first_name' => 'Jane',
            'last_name' => 'Online',
            'email' => 'jane-online@test.com',
            'password' => Hash::make('password'),
            'role' => 2,
            'status' => 1,
        ]);

        DB::table('sessions')->insert([
            'id' => 'session-jane',
            'user_id' => 1,
            'last_activity' => Carbon::now()->timestamp,
        ]);

        $cacheKey = 'active_staff_v2_5_'.md5(json_encode([null, null, null, 'name', 'asc']));

        $first = $this->service->getActiveStaff(5);
        $cached = Cache::get($cacheKey);
        $second = $this->service->getActiveStaff(5);

        $this->assertIsArray($cached);
        $this->assertSame(1, $first['meta']['total']);
        $this->assertSame('Jane Online', $first['data']->first()['name']);
        $this->assertSame($first['meta']['total'], $second['meta']['total']);
        $this->assertSame($first['data']->first()['email'], $second['data']->first()['email']);
    }

    private function createSchema(): void
    {
        Schema::dropIfExists('staff_login_logs');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('branches');

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->unsignedInteger('role')->nullable();
            $table->string('team')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedInteger('status')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_login_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('level')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('last_activity')->nullable();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('office_name')->nullable();
            $table->timestamps();
        });

        DB::table('user_roles')->insert([
            'id' => 2,
            'name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
