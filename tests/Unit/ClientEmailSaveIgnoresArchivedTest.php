<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\ClientPersonalDetailsController;
use App\Models\Admin;
use App\Models\ClientEmail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class ClientEmailSaveIgnoresArchivedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
    }

    public function test_archived_primary_email_does_not_block_edit(): void
    {
        $active = Admin::factory()->create([
            'type' => 'client',
            'email' => 'vipul_devlop.bi@outlook.com',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        Admin::factory()->create([
            'type' => 'lead',
            'email' => 'viplucmca@yahoo.co.in',
            'is_company' => 0,
            'is_archived' => 1,
        ]);

        $this->assertFalse($this->emailConflicts('viplucmca@yahoo.co.in', (int) $active->id));
    }

    public function test_active_primary_email_still_blocks_edit(): void
    {
        $active = Admin::factory()->create([
            'type' => 'client',
            'email' => 'current.client@test.com',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        Admin::factory()->create([
            'type' => 'client',
            'email' => 'viplucmca@yahoo.co.in',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        $this->assertTrue($this->emailConflicts('viplucmca@yahoo.co.in', (int) $active->id));
    }

    public function test_archived_extra_email_does_not_block_edit(): void
    {
        $active = Admin::factory()->create([
            'type' => 'client',
            'email' => 'current.client@test.com',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        $archived = Admin::factory()->create([
            'type' => 'lead',
            'email' => 'archived.primary@test.com',
            'is_company' => 0,
            'is_archived' => 1,
        ]);

        ClientEmail::create([
            'client_id' => $archived->id,
            'email' => 'viplucmca@yahoo.co.in',
        ]);

        $this->assertFalse($this->emailConflicts('viplucmca@yahoo.co.in', (int) $active->id));
    }

    public function test_active_extra_email_still_blocks_edit(): void
    {
        $active = Admin::factory()->create([
            'type' => 'client',
            'email' => 'current.client@test.com',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        $other = Admin::factory()->create([
            'type' => 'client',
            'email' => 'other.primary@test.com',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        ClientEmail::create([
            'client_id' => $other->id,
            'email' => 'viplucmca@yahoo.co.in',
        ]);

        $this->assertTrue($this->emailConflicts('viplucmca@yahoo.co.in', (int) $active->id));
    }

    public function test_null_is_archived_is_treated_as_unarchived(): void
    {
        $active = Admin::factory()->create([
            'type' => 'client',
            'email' => 'current.client@test.com',
            'is_company' => 0,
            'is_archived' => 0,
        ]);

        Admin::factory()->create([
            'type' => 'lead',
            'email' => 'legacy@test.com',
            'is_company' => 0,
            'is_archived' => null,
        ]);

        $this->assertTrue($this->emailConflicts('legacy@test.com', (int) $active->id));
    }

    private function emailConflicts(string $email, int $clientId, ?int $emailId = null): bool
    {
        $method = new ReflectionMethod(ClientPersonalDetailsController::class, 'emailExistsOnAnotherUnarchivedClient');
        $method->setAccessible(true);

        return (bool) $method->invoke(new ClientPersonalDetailsController, $email, $clientId, $emailId);
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('password')->nullable();
                $table->string('type')->nullable();
                $table->string('country')->nullable();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->string('address')->nullable();
                $table->string('zip')->nullable();
                $table->integer('status')->nullable();
                $table->date('dob')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->unsignedInteger('is_archived')->nullable();
                $table->unsignedTinyInteger('is_company')->nullable();
                $table->string('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_emails')) {
            Schema::create('client_emails', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('email')->nullable();
                $table->string('email_type')->nullable();
                $table->timestamps();
            });
        }
    }
}
