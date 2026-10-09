<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LeadListFieldUpdateTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();

        $this->staff = Staff::create([
            'first_name' => 'Lead',
            'last_name' => 'Lister',
            'email' => 'lead-lister@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
    }

    public function test_list_field_update_changes_source(): void
    {
        $lead = Lead::create([
            'type' => 'lead',
            'client_id' => 'LST3001',
            'first_name' => 'Quick',
            'last_name' => 'Source',
            'email' => 'quick.source@test.com',
            'phone' => '0400000099',
            'status' => '1',
            'lead_status' => 'new',
            'source' => null,
            'is_archived' => 0,
        ]);

        $encodedId = base64_encode(convert_uuencode((string) $lead->id));

        $response = $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.list_field.update', $encodedId), [
                'field' => 'source',
                'source' => 'Website',
            ]);

        $response->assertOk();
        $response->assertJsonPath('status', 1);
        $response->assertJsonPath('source_display', 'Website');

        $lead->refresh();
        $this->assertSame('Website', $lead->source);
    }

    public function test_list_field_update_changes_stage_and_record_status(): void
    {
        $lead = Lead::create([
            'type' => 'lead',
            'client_id' => 'LST3002',
            'first_name' => 'Quick',
            'last_name' => 'Stage',
            'email' => 'quick.stage@test.com',
            'phone' => '0400000100',
            'status' => '1',
            'lead_status' => 'new',
            'is_archived' => 0,
        ]);

        $encodedId = base64_encode(convert_uuencode((string) $lead->id));

        $response = $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.list_field.update', $encodedId), [
                'field' => 'stage',
                'lead_status' => 'not_qualified',
                'followup_date' => '',
            ]);

        $response->assertOk();
        $response->assertJsonPath('status', 1);
        $response->assertJsonPath('lead_status', 'not_qualified');
        $response->assertJsonPath('record_status', 0);

        $lead->refresh();
        $this->assertSame('not_qualified', $lead->lead_status);
        $this->assertSame(0, (int) $lead->status);
    }

    private function createSchema(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->text('module_access')->nullable();
            $table->timestamps();
        });

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

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->nullable();
            $table->integer('status')->nullable();
            $table->string('lead_status')->nullable();
            $table->string('source')->nullable();
            $table->unsignedTinyInteger('is_archived')->default(0);
            $table->unsignedTinyInteger('is_deleted')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('followup_date')->nullable();
            $table->timestamps();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('type')->nullable();
            $table->unsignedTinyInteger('is_action')->nullable();
            $table->unsignedTinyInteger('pin')->nullable();
            $table->string('status')->nullable();
            $table->string('task_group')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('action_date')->nullable();
            $table->text('description')->nullable();
            $table->string('title')->nullable();
            $table->string('unique_group_id')->nullable();
            $table->timestamps();
        });
    }
}
