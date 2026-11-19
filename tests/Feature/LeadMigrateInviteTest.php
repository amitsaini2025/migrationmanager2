<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\LeadMigrateInviteMail;
use App\Mail\LeadMigrateInviteReceptionMail;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\CheckinLog;
use App\Models\ClientExperience;
use App\Models\ClientQualification;
use App\Models\ClientVisaCountry;
use App\Models\LeadMigrateInvite;
use App\Models\Staff;
use App\Services\Sms\UnifiedSmsManager;
use App\Support\LeadMigrateInviteChannels;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadMigrateInviteTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();

        $this->staff = Staff::query()->create([
            'first_name' => 'Migrate',
            'last_name' => 'Staff',
            'email' => 'migrate-invite-staff@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $branch = Branch::query()->create([
            'office_name' => 'Test Office',
            'email' => 'office@test.com',
        ]);

        config([
            'lead_migrate_invite.default_office_id' => $branch->id,
            'constants.reception_user_id' => $this->staff->id,
        ]);
    }

    #[Test]
    public function duplicate_email_is_rejected_before_sending(): void
    {
        Mail::fake();

        Admin::query()->create([
            'type' => 'lead',
            'first_name' => 'Existing',
            'last_name' => 'Lead',
            'email' => 'taken@example.com',
            'phone' => '0499999999',
            'is_archived' => 0,
            'is_deleted' => null,
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.migrate-invite.send'), [
                'channel' => 'email',
                'email' => 'taken@example.com',
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Email already exist in our crm. Pls try other.',
            ]);

        Mail::assertNothingSent();
        $this->assertSame(0, LeadMigrateInvite::query()->count());
    }

    #[Test]
    public function archived_email_and_phone_do_not_block_migrate_invite(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')
                ->once()
                ->andReturn(['success' => true]);
        });

        Admin::query()->create([
            'type' => 'lead',
            'first_name' => 'Archived',
            'last_name' => 'Email',
            'email' => 'archived.email@example.com',
            'phone' => '0488888888',
            'is_archived' => 1,
            'is_deleted' => null,
        ]);

        Admin::query()->create([
            'type' => 'client',
            'first_name' => 'Archived',
            'last_name' => 'Phone',
            'email' => 'archived.phone@example.com',
            'phone' => '0477777777',
            'is_archived' => 1,
            'is_deleted' => null,
        ]);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.migrate-invite.send'), [
                'channel' => 'email',
                'email' => 'archived.email@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.migrate-invite.send'), [
                'channel' => 'sms',
                'phone' => '0477 777 777',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        Mail::assertSent(LeadMigrateInviteMail::class);
    }

    #[Test]
    public function email_invite_sends_mail_and_public_submit_creates_differentiated_lead(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.migrate-invite.send'), [
                'channel' => 'email',
                'email' => 'new.migrate@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        Mail::assertSent(LeadMigrateInviteMail::class);

        $invite = LeadMigrateInvite::query()->latest('id')->first();
        $this->assertNotNull($invite);
        $this->assertTrue($invite->isUsable());
        $this->assertSame(LeadMigrateInviteChannels::SOURCE_EMAIL, $invite->source);
        $this->assertSame('email', $invite->channel);

        $plainToken = 'migrateemailtokenmigrateemailtokenmigrateemailtokenmigrateema12';
        $invite->update(['token_hash' => LeadMigrateInvite::hashToken($plainToken)]);

        $this->get(route('public.lead-migrate-invite.show', ['token' => $plainToken]))
            ->assertOk()
            ->assertSee('new.migrate@example.com', false)
            ->assertDontSee('cannot be changed', false);

        $this->post(route('public.lead-migrate-invite.submit', ['token' => $plainToken]), [
            'first_name' => 'Asha',
            'last_name' => 'Patel',
            'phone' => '0412345678',
            'email' => 'changed.migrate@example.com',
            'city' => 'Melbourne, VIC',
            'current_visa' => 'Student (500)',
            'visa_expiry' => '2027-01-31',
            'qualification' => 'Bachelor Degree',
            'qualification_date' => '2020-06-15',
            'occupation' => 'Software Engineer',
        ])->assertOk()
            ->assertSee('Thank you');

        $lead = Admin::query()
            ->where('type', 'lead')
            ->where('email', 'changed.migrate@example.com')
            ->first();

        $this->assertNotNull($lead);
        $this->assertSame('0412345678', $lead->phone);
        $this->assertSame(LeadMigrateInviteChannels::SOURCE_EMAIL, $lead->source);
        $this->assertSame(1, (int) $lead->manual_form_fill);
        $this->assertSame('Asha', $lead->first_name);
        $this->assertNull($lead->country);
        $this->assertSame('Melbourne, VIC', $lead->city);

        $qualification = ClientQualification::query()->where('client_id', $lead->id)->first();
        $this->assertNotNull($qualification);
        $this->assertSame('Bachelor Degree', $qualification->level);
        $this->assertSame('2020-06-15', (string) $qualification->finish_date);

        $experience = ClientExperience::query()->where('client_id', $lead->id)->first();
        $this->assertNotNull($experience);
        $this->assertSame('Software Engineer', $experience->job_title);
        $this->assertNull($experience->job_country);
        $this->assertNull($experience->job_state);
        $this->assertNull($experience->job_type);

        $visa = ClientVisaCountry::query()->where('client_id', $lead->id)->first();
        $this->assertNotNull($visa);
        $this->assertSame('2027-01-31', (string) $visa->visa_expiry_date);
        $this->assertSame('Student (500)', (string) $visa->visa_description);

        $visit = CheckinLog::query()->where('client_id', $lead->id)->first();
        $this->assertNotNull($visit);
        $this->assertSame(0, (int) $visit->status);
        $this->assertSame('Lead', $visit->contact_type);
        $this->assertStringContainsString('Migration Client Enquiry', (string) $visit->visit_purpose);
        Mail::assertSent(LeadMigrateInviteReceptionMail::class, function (LeadMigrateInviteReceptionMail $mail): bool {
            return $mail->hasTo('migrate-invite-staff@test.com');
        });

        $invite->refresh();
        $this->assertFalse($invite->isUsable());
        $this->assertSame((int) $lead->id, (int) $invite->lead_id);

        $this->get(route('public.lead-migrate-invite.show', ['token' => $plainToken]))
            ->assertOk()
            ->assertSee('This link is no longer available');
    }

    #[Test]
    public function sms_invite_rejects_non_australian_mobile_and_sends_for_valid_number(): void
    {
        Mail::fake();

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.migrate-invite.send'), [
                'channel' => 'sms',
                'phone' => '0212345678',
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')
                ->once()
                ->withArgs(function (string $to, string $message): bool {
                    return $to === '+610412345678'
                        && str_contains($message, '/migrate-lead/')
                        && str_contains($message, 'Bansal Immigration');
                })
                ->andReturn(['success' => true]);
        });

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.migrate-invite.send'), [
                'channel' => 'sms',
                'phone' => '0412 345 678',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $invite = LeadMigrateInvite::query()->latest('id')->first();
        $this->assertNotNull($invite);
        $this->assertSame('0412345678', $invite->sent_to_phone);
        $this->assertSame(LeadMigrateInviteChannels::SOURCE_SMS, $invite->source);
        Mail::assertNothingSent();
    }

    #[Test]
    public function duplicate_phone_is_rejected_before_sending_sms(): void
    {
        Admin::query()->create([
            'type' => 'client',
            'first_name' => 'Existing',
            'last_name' => 'Client',
            'email' => 'phone-taken@example.com',
            'phone' => '0411111111',
            'is_archived' => 0,
            'is_deleted' => null,
        ]);

        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $this->actingAs($this->staff, 'admin')
            ->postJson(route('leads.migrate-invite.send'), [
                'channel' => 'sms',
                'phone' => '0411 111 111',
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Contact number already exist in our crm. Pls try other.',
            ]);
    }

    #[Test]
    public function service_normalizes_australian_mobile_variants(): void
    {
        $this->assertSame('0412345678', LeadMigrateInviteChannels::normalizeAustralianMobile('+61 412 345 678'));
        $this->assertSame('0412345678', LeadMigrateInviteChannels::normalizeAustralianMobile('61412345678'));
        $this->assertNull(LeadMigrateInviteChannels::normalizeAustralianMobile('0399998888'));
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

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('type')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('country_code')->nullable();
                $table->string('password')->nullable();
                $table->string('gender')->nullable();
                $table->string('marital_status')->nullable();
                $table->string('dob')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('country')->nullable();
                $table->string('country_passport')->nullable();
                $table->string('visa_type')->nullable();
                $table->string('source')->nullable();
                $table->unsignedTinyInteger('manual_form_fill')->nullable();
                $table->string('lead_status')->nullable();
                $table->date('followup_date')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('client_id')->nullable();
                $table->string('client_counter')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->unsignedInteger('verified')->nullable();
                $table->unsignedInteger('cp_status')->nullable();
                $table->unsignedInteger('cp_code_verify')->nullable();
                $table->unsignedInteger('australian_study')->nullable();
                $table->unsignedInteger('specialist_education')->nullable();
                $table->unsignedInteger('regional_study')->nullable();
                $table->unsignedInteger('is_company')->nullable();
                $table->unsignedInteger('is_archived')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->timestamps();
            });
        } else {
            foreach ([
                'country_code', 'gender', 'marital_status', 'dob', 'address', 'city', 'country',
                'country_passport', 'visa_type', 'source', 'manual_form_fill', 'lead_status',
                'followup_date', 'client_counter', 'verified', 'cp_status', 'cp_code_verify',
                'australian_study', 'specialist_education', 'regional_study', 'is_company',
                'is_archived', 'is_deleted', 'password', 'status',
            ] as $column) {
                if (! Schema::hasColumn('admins', $column)) {
                    Schema::table('admins', function (Blueprint $table) use ($column) {
                        if (in_array($column, ['manual_form_fill', 'verified', 'cp_status', 'cp_code_verify', 'australian_study', 'specialist_education', 'regional_study', 'is_company', 'is_archived', 'status'], true)) {
                            $table->unsignedInteger($column)->nullable();
                        } elseif ($column === 'followup_date') {
                            $table->date($column)->nullable();
                        } else {
                            $table->string($column)->nullable();
                        }
                    });
                }
            }
        }

        if (! Schema::hasTable('client_emails')) {
            Schema::create('client_emails', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('email')->nullable();
                $table->string('email_type')->nullable();
                $table->boolean('is_verified')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_contacts')) {
            Schema::create('client_contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('phone')->nullable();
                $table->string('country_code')->nullable();
                $table->string('contact_type')->nullable();
                $table->boolean('is_verified')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lead_migrate_invites')) {
            Schema::create('lead_migrate_invites', function (Blueprint $table) {
                $table->id();
                $table->string('token_hash', 64)->unique();
                $table->string('channel', 16)->index();
                $table->string('sent_to_email', 255)->nullable();
                $table->string('sent_to_phone', 32)->nullable();
                $table->string('sent_to_phone_e164', 32)->nullable();
                $table->unsignedInteger('sent_by')->nullable();
                $table->unsignedInteger('lead_id')->nullable();
                $table->string('source', 64);
                $table->json('submitted_payload')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamp('invalidated_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_qualifications')) {
            Schema::create('client_qualifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('level')->nullable();
                $table->string('name')->nullable();
                $table->string('qual_college_name')->nullable();
                $table->string('qual_campus')->nullable();
                $table->string('country')->nullable();
                $table->string('qual_state')->nullable();
                $table->date('start_date')->nullable();
                $table->date('finish_date')->nullable();
                $table->unsignedTinyInteger('relevant_qualification')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_experiences')) {
            Schema::create('client_experiences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('job_title')->nullable();
                $table->string('job_code')->nullable();
                $table->string('job_country')->nullable();
                $table->string('job_emp_name')->nullable();
                $table->string('job_state')->nullable();
                $table->string('job_type')->nullable();
                $table->date('job_start_date')->nullable();
                $table->date('job_finish_date')->nullable();
                $table->unsignedTinyInteger('relevant_experience')->nullable();
                $table->decimal('fte_multiplier', 5, 2)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_visa_countries')) {
            Schema::create('client_visa_countries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedInteger('visa_type')->nullable();
                $table->text('visa_description')->nullable();
                $table->date('visa_expiry_date')->nullable();
                $table->date('visa_grant_date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('matters')) {
            Schema::create('matters', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('nick_name')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('office_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('checkin_logs')) {
            Schema::create('checkin_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('walk_in_phone')->nullable();
                $table->string('walk_in_email')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('visit_purpose')->nullable();
                $table->unsignedBigInteger('office')->nullable();
                $table->string('contact_type')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->date('date')->nullable();
                $table->unsignedInteger('wait_type')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('checkin_histories')) {
            Schema::create('checkin_histories', function (Blueprint $table) {
                $table->id();
                $table->string('subject')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('checkin_id')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('notification_type')->nullable();
                $table->unsignedBigInteger('module_id')->nullable();
                $table->unsignedBigInteger('receiver_id')->nullable();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('url')->nullable();
                $table->text('message')->nullable();
                $table->unsignedTinyInteger('seen')->nullable();
                $table->unsignedTinyInteger('receiver_status')->nullable();
                $table->unsignedTinyInteger('sender_status')->nullable();
                $table->timestamps();
            });
        }
    }
}
