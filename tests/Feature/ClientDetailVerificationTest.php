<?php

namespace Tests\Feature;

use App\Http\Controllers\CRM\ClientPersonalDetailsController;
use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\ClientDetailVerificationMail;
use App\Models\Admin;
use App\Models\ClientDetailVerification;
use App\Models\ClientDetailVerificationField;
use App\Models\Staff;
use App\Services\ClientDetailVerificationService;
use App\Services\Sms\UnifiedSmsManager;
use App\Support\ClientDetailVerificationFields;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientDetailVerificationTest extends TestCase
{
    protected Staff $staff;

    protected Admin $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();

        $this->staff = Staff::query()->create([
            'first_name' => 'Super1',
            'last_name' => 'Admin1',
            'email' => 'verify-link-staff@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $this->client = Admin::query()->create([
            'type' => 'client',
            'first_name' => 'Vipul',
            'last_name' => 'Kumar',
            'email' => 'vipul.primary@example.com',
            'phone' => '0412345678',
            'country_code' => '+61',
            'gender' => 'Other',
            'marital_status' => 'Married',
            'is_company' => 0,
            'is_deleted' => null,
        ]);
    }

    #[Test]
    public function sending_a_new_link_invalidates_an_unused_previous_link(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')
                ->once()
                ->withArgs(function (string $to, string $message, string $type, array $context): bool {
                    return $to === '+610412345678'
                        && $type === 'notification'
                        && str_contains($message, 'Hi Vipul, Bansal Immigration Consultants requests you to verify your Personal & Visa details currently recorded on your file.')
                        && str_contains($message, 'Please review and confirm or request any corrections using the secure link below:')
                        && str_contains($message, 'It should only take 1–2 minutes. Please do not forward this personalised link to anyone else.')
                        && str_contains($message, '/verify-details/')
                        && ($context['client_id'] ?? null) === $this->client->id;
                })
                ->andReturn(['success' => true]);
        });

        $service = app(ClientDetailVerificationService::class);

        $first = $this->createOpenVerification('old-token-old-token-old-token-old-token-old-tok');
        $this->assertTrue($first->fresh()->isUsable());

        $result = $service->sendLink($this->client, $this->staff->id, ClientDetailVerificationFields::CHANNEL_SMS);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('+610412345678', $result['message']);
        $this->assertNotNull($first->fresh()->invalidated_at);
        $this->assertFalse($first->fresh()->isUsable());
        Mail::assertNothingSent();
        Mail::assertNotSent(ClientDetailVerificationMail::class);
    }

    #[Test]
    public function a_successful_email_send_keeps_the_new_link_usable(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $first = $this->createOpenVerification('old-token-old-token-old-token-old-token-old-tok');

        $result = app(ClientDetailVerificationService::class)->sendLink(
            $this->client,
            $this->staff->id,
            ClientDetailVerificationFields::CHANNEL_EMAIL,
        );

        $this->assertTrue($result['success']);
        $this->assertFalse($first->fresh()->isUsable());

        $latest = ClientDetailVerification::query()->latest('id')->first();
        $this->assertNotNull($latest);
        $this->assertTrue($latest->isUsable());
        $this->assertNotSame($first->id, $latest->id);
    }

    #[Test]
    public function a_failed_email_send_does_not_kill_the_token_in_case_zoho_already_delivered(): void
    {
        Mail::shouldReceive('mailer')->andThrow(new \RuntimeException('Transport "ses" failed.'));
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $first = $this->createOpenVerification('old-token-old-token-old-token-old-token-old-tok');

        $result = app(ClientDetailVerificationService::class)->sendLink(
            $this->client,
            $this->staff->id,
            ClientDetailVerificationFields::CHANNEL_EMAIL,
        );

        $this->assertFalse($result['success']);
        $this->assertTrue($first->fresh()->isUsable());

        $latest = ClientDetailVerification::query()->latest('id')->first();
        $this->assertNotNull($latest);
        $this->assertTrue($latest->isUsable());
    }

    #[Test]
    public function sending_a_link_by_email_does_not_send_sms(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $result = app(ClientDetailVerificationService::class)->sendLink(
            $this->client,
            $this->staff->id,
            ClientDetailVerificationFields::CHANNEL_EMAIL,
        );

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('vipul.primary@example.com', $result['message']);
        Mail::assertSent(ClientDetailVerificationMail::class, function (ClientDetailVerificationMail $mail): bool {
            return $mail->hasTo('vipul.primary@example.com') && $mail->firstName === 'Vipul';
        });
    }

    #[Test]
    public function sending_a_link_requires_a_primary_phone_and_does_not_send_sms_or_email(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $this->client->forceFill([
            'phone' => '',
            'country_code' => '',
        ])->save();

        $result = app(ClientDetailVerificationService::class)->sendLink(
            $this->client,
            $this->staff->id,
            ClientDetailVerificationFields::CHANNEL_SMS,
        );

        $this->assertFalse($result['success']);
        $this->assertSame('This client has no primary phone number.', $result['message']);
        Mail::assertNothingSent();
    }

    #[Test]
    public function sending_a_link_requires_a_primary_email_when_email_channel_is_chosen(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $this->client->forceFill([
            'email' => '',
        ])->save();

        $result = app(ClientDetailVerificationService::class)->sendLink(
            $this->client,
            $this->staff->id,
            ClientDetailVerificationFields::CHANNEL_EMAIL,
        );

        $this->assertFalse($result['success']);
        $this->assertSame('This client has no primary email address.', $result['message']);
        Mail::assertNothingSent();
    }

    #[Test]
    public function sending_a_link_rejects_an_unknown_channel(): void
    {
        Mail::fake();
        $this->mock(UnifiedSmsManager::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendSms')->never();
        });

        $result = app(ClientDetailVerificationService::class)->sendLink(
            $this->client,
            $this->staff->id,
            'whatsapp',
        );

        $this->assertFalse($result['success']);
        $this->assertSame('Please choose Primary email or Primary phone.', $result['message']);
        Mail::assertNothingSent();
    }

    #[Test]
    public function public_form_shows_snapshot_and_submit_expires_the_token(): void
    {
        $token = 'usabletokenusabletokenusabletokenusabletokenusabletokenusable12';
        $verification = $this->createOpenVerification($token, [
            'full_name' => 'Vipul Kumar',
            'dob' => 'N/A',
            'gender' => 'Other',
            'marital_status' => 'Married',
            'email' => 'vipul.primary@example.com',
            'phone' => '0412345678',
            'address' => 'N/A',
            'visa_type' => 'N/A',
            'visa_expiry' => 'N/A',
            'passport_country' => 'N/A',
            'location_status' => 'N/A',
        ]);

        $this->get(route('public.client-detail-verification.show', ['token' => $token]))
            ->assertOk()
            ->assertSee('Vipul Kumar')
            ->assertSee('vipul.primary@example.com')
            ->assertSee('Search Address')
            ->assertSee('Address Line 1')
            ->assertSee('client-detail-verification-address.js')
            ->assertSee('Visa Type / Subclass')
            ->assertSee('client-detail-verification-visa.js')
            ->assertDontSee('address-autocomplete.js')
            ->assertDontSee('edit-client.js')
            ->assertDontSee('secondary@example.com');

        $payload = $this->confirmedPayload();
        $payload[4]['status'] = ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED;
        $payload[4]['requested_value'] = 'vipul.new@example.com';

        $this->post(route('public.client-detail-verification.submit', ['token' => $token]), [
            'declaration' => '1',
            'fields_json' => json_encode($payload),
        ])->assertOk()
            ->assertSee('Verification Confirmed and Request Changes')
            ->assertSee('Verified By:')
            ->assertSee('Super1 Admin1')
            ->assertSee('Verified At:')
            ->assertSee(ClientDetailVerificationFields::formatVerifiedAt(now()))
            ->assertDontSee('Submit Verification');

        $this->assertFalse($verification->fresh()->isUsable());
        $this->assertNotNull($verification->fresh()->used_at);

        $this->get(route('public.client-detail-verification.show', ['token' => $token]))
            ->assertOk()
            ->assertSee('This link is no longer valid');

        $emailField = ClientDetailVerificationField::query()
            ->where('verification_id', $verification->id)
            ->where('field_key', 'email')
            ->first();

        $this->assertNotNull($emailField);
        $this->assertSame(ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED, $emailField->status);
        $this->assertSame('vipul.new@example.com', $emailField->requested_value);
        $this->assertSame('vipul.primary@example.com', $this->client->fresh()->email);
    }

    #[Test]
    public function public_submit_shows_confirmed_heading_when_every_field_is_confirmed(): void
    {
        $token = 'confirmalltokenconfirmalltokenconfirmalltokenconfirmalltoken12';
        $this->createOpenVerification($token);

        $this->post(route('public.client-detail-verification.submit', ['token' => $token]), [
            'declaration' => '1',
            'fields_json' => json_encode($this->confirmedPayload()),
        ])->assertOk()
            ->assertSee('Verification Confirmed')
            ->assertDontSee('Verification Confirmed and Request Changes')
            ->assertSee('Verified By:')
            ->assertSee('Super1 Admin1')
            ->assertSee('Verified At:')
            ->assertSee(ClientDetailVerificationFields::formatVerifiedAt(now()));

        $summary = app(ClientDetailVerificationService::class)->latestSubmittedSummary((int) $this->client->id);
        $this->assertNotNull($summary);
        $this->assertSame('Verification Confirmed', $summary['heading']);
        $this->assertSame('Super1 Admin1', $summary['verified_by']);
        $this->assertSame(ClientDetailVerificationFields::formatVerifiedAt(now()), $summary['verified_at']);
    }

    #[Test]
    public function public_submit_shows_request_change_heading_when_every_field_is_changed(): void
    {
        $token = 'changealltokenchangealltokenchangealltokenchangealltokenchang12';
        $this->createOpenVerification($token);

        $payload = array_map(static function (array $field): array {
            $field['status'] = ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED;
            $field['requested_value'] = 'Updated '.$field['key'];

            return $field;
        }, $this->confirmedPayload());

        $this->post(route('public.client-detail-verification.submit', ['token' => $token]), [
            'declaration' => '1',
            'fields_json' => json_encode($payload),
        ])->assertOk()
            ->assertSee('Request Change')
            ->assertDontSee('Verification Confirmed')
            ->assertSee('Super1 Admin1')
            ->assertSee(ClientDetailVerificationFields::formatVerifiedAt(now()));
    }

    #[Test]
    public function public_address_search_uses_the_same_backend_as_client_edit_when_the_link_is_valid(): void
    {
        $token = 'searchtokensearchtokensearchtokensearchtokensearchtokensearc12';
        $this->createOpenVerification($token);

        $this->mock(ClientPersonalDetailsController::class, function (MockInterface $mock): void {
            $mock->shouldReceive('searchAddressFull')
                ->once()
                ->andReturn(response()->json([
                    'status' => 'OK',
                    'predictions' => [
                        [
                            'place_id' => 'ChIJtest',
                            'description' => '10 Oliva Approach, Piara Waters WA, Australia',
                        ],
                    ],
                ]));
        });

        $this->postJson(route('public.client-detail-verification.search-address', ['token' => $token]), [
            'query' => '10 Oliva Approach',
        ])->assertOk()->assertJsonPath('predictions.0.description', '10 Oliva Approach, Piara Waters WA, Australia');
    }

    #[Test]
    public function public_address_search_rejects_an_expired_link_and_does_not_call_places(): void
    {
        $token = 'expiredtokenexpiredtokenexpiredtokenexpiredtokenexpiredtokenex12';
        $verification = $this->createOpenVerification($token);
        $verification->forceFill(['used_at' => now()])->save();

        $this->mock(ClientPersonalDetailsController::class, function (MockInterface $mock): void {
            $mock->shouldReceive('searchAddressFull')->never();
            $mock->shouldReceive('getPlaceDetails')->never();
        });

        $this->postJson(route('public.client-detail-verification.search-address', ['token' => $token]), [
            'query' => '10 Oliva Approach',
        ])->assertNotFound();
    }

    #[Test]
    public function public_address_search_requires_at_least_three_characters(): void
    {
        $token = 'querytokenquerytokenquerytokenquerytokenquerytokenquerytoken12';
        $this->createOpenVerification($token);

        $this->mock(ClientPersonalDetailsController::class, function (MockInterface $mock): void {
            $mock->shouldReceive('searchAddressFull')->never();
        });

        $this->postJson(route('public.client-detail-verification.search-address', ['token' => $token]), [
            'query' => '10',
        ])->assertStatus(422)->assertJsonValidationErrors(['query']);
    }

    #[Test]
    public function public_form_can_submit_a_composed_residential_address_change(): void
    {
        $token = 'addresstokenaddresstokenaddresstokenaddresstokenaddresstokenad12';
        $verification = $this->createOpenVerification($token, [
            'full_name' => 'Vipul Kumar',
            'dob' => 'N/A',
            'gender' => 'Other',
            'marital_status' => 'Married',
            'email' => 'vipul.primary@example.com',
            'phone' => '0412345678',
            'address' => 'N/A',
            'visa_type' => 'N/A',
            'visa_expiry' => 'N/A',
            'passport_country' => 'N/A',
            'location_status' => 'N/A',
        ]);

        $payload = $this->confirmedPayload();
        $addressIndex = array_search('address', ClientDetailVerificationFields::keys(), true);
        $this->assertNotFalse($addressIndex);
        $payload[$addressIndex]['status'] = ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED;
        $payload[$addressIndex]['requested_value'] = '10 Oliva Approach, Piara Waters, WA, Australia, 6112';

        $this->post(route('public.client-detail-verification.submit', ['token' => $token]), [
            'declaration' => '1',
            'fields_json' => json_encode($payload),
        ])->assertOk()
            ->assertSee('Verification Confirmed and Request Changes')
            ->assertSee('Super1 Admin1')
            ->assertSee(ClientDetailVerificationFields::formatVerifiedAt(now()));

        $addressField = ClientDetailVerificationField::query()
            ->where('verification_id', $verification->id)
            ->where('field_key', 'address')
            ->first();

        $this->assertNotNull($addressField);
        $this->assertSame(ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED, $addressField->status);
        $this->assertSame('10 Oliva Approach, Piara Waters, WA, Australia, 6112', $addressField->requested_value);
    }

    #[Test]
    public function public_visa_types_use_the_same_matter_list_as_client_edit_when_the_link_is_valid(): void
    {
        $this->createMattersTable();
        DB::table('matters')->insert([
            ['id' => 10, 'title' => '151 - Former Resident', 'nick_name' => 'FR', 'status' => 1],
            ['id' => 11, 'title' => '485 skill assessment', 'nick_name' => 'SA', 'status' => 1],
            ['id' => 12, 'title' => 'Inactive Visa', 'nick_name' => 'IN', 'status' => 0],
        ]);

        $token = 'visatypestokenvisatypestokenvisatypestokenvisatypestokenvisaty12';
        $this->createOpenVerification($token);

        $this->getJson(route('public.client-detail-verification.visa-types', ['token' => $token]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.label', '151 - Former Resident (FR)')
            ->assertJsonMissing(['title' => '485 skill assessment'])
            ->assertJsonMissing(['title' => 'Inactive Visa']);
    }

    #[Test]
    public function public_visa_types_reject_an_expired_link(): void
    {
        $token = 'expiredvisatokenexpiredvisatokenexpiredvisatokenexpiredvisatok12';
        $verification = $this->createOpenVerification($token);
        $verification->forceFill(['used_at' => now()])->save();

        $this->getJson(route('public.client-detail-verification.visa-types', ['token' => $token]))
            ->assertNotFound();
    }

    #[Test]
    public function public_form_can_submit_a_selected_visa_type_change(): void
    {
        $token = 'visachangetokenvisachangetokenvisachangetokenvisachangetokenvi12';
        $verification = $this->createOpenVerification($token, [
            'full_name' => 'Vipul Kumar',
            'dob' => 'N/A',
            'gender' => 'Other',
            'marital_status' => 'Married',
            'email' => 'vipul.primary@example.com',
            'phone' => '0412345678',
            'address' => 'N/A',
            'visa_type' => 'N/A',
            'visa_expiry' => 'N/A',
            'passport_country' => 'N/A',
            'location_status' => 'N/A',
        ]);

        $payload = $this->confirmedPayload();
        $visaIndex = array_search('visa_type', ClientDetailVerificationFields::keys(), true);
        $this->assertNotFalse($visaIndex);
        $payload[$visaIndex]['status'] = ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED;
        $payload[$visaIndex]['requested_value'] = '151 - Former Resident (FR)';

        $this->post(route('public.client-detail-verification.submit', ['token' => $token]), [
            'declaration' => '1',
            'fields_json' => json_encode($payload),
        ])->assertOk()
            ->assertSee('Verification Confirmed and Request Changes')
            ->assertSee('Super1 Admin1');

        $visaField = ClientDetailVerificationField::query()
            ->where('verification_id', $verification->id)
            ->where('field_key', 'visa_type')
            ->first();

        $this->assertNotNull($visaField);
        $this->assertSame(ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED, $visaField->status);
        $this->assertSame('151 - Former Resident (FR)', $visaField->requested_value);
    }

    #[Test]
    public function team_accept_finalizes_the_requested_primary_email(): void
    {
        $this->actingAs($this->staff, 'admin');

        $field = ClientDetailVerificationField::query()->create([
            'verification_id' => $this->createOpenVerification('accepttokenaccepttokenaccepttokenaccepttokenaccepttokenacce12')->id,
            'client_id' => $this->client->id,
            'field_key' => 'email',
            'original_value' => 'vipul.primary@example.com',
            'requested_value' => 'vipul.final@example.com',
            'status' => ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED,
        ]);

        $response = $this->post(route('clients.detail-verification.accept', ['field' => $field->id]));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'display_value' => 'vipul.final@example.com',
                'field_key' => 'email',
            ]);
        $this->assertStringContainsString('title="Confirmed"', (string) $response->json('confirmed_icon'));

        $this->assertSame('vipul.final@example.com', $this->client->fresh()->email);
        $this->assertSame(ClientDetailVerificationFields::STATUS_ACCEPTED, $field->fresh()->status);
    }

    #[Test]
    public function latest_submitted_summary_ignores_unsubmitted_links_and_keeps_heading_after_accept(): void
    {
        $this->createOpenVerification('unusedsummarytokenunusedsummarytokenunusedsummarytokenunuse12');

        $this->assertNull(
            app(ClientDetailVerificationService::class)->latestSubmittedSummary((int) $this->client->id)
        );

        $token = 'mixedsummarytokenmixedsummarytokenmixedsummarytokenmixedsumma12';
        $verification = $this->createOpenVerification($token);

        $payload = $this->confirmedPayload();
        $payload[4]['status'] = ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED;
        $payload[4]['requested_value'] = 'vipul.sidebar@example.com';

        $this->post(route('public.client-detail-verification.submit', ['token' => $token]), [
            'declaration' => '1',
            'fields_json' => json_encode($payload),
        ])->assertOk();

        $summary = app(ClientDetailVerificationService::class)->latestSubmittedSummary((int) $this->client->id);
        $this->assertNotNull($summary);
        $this->assertSame('Verification Confirmed and Request Changes', $summary['heading']);
        $this->assertSame('Super1 Admin1', $summary['verified_by']);
        $this->assertSame(ClientDetailVerificationFields::formatVerifiedAt(now()), $summary['verified_at']);

        $emailField = ClientDetailVerificationField::query()
            ->where('verification_id', $verification->id)
            ->where('field_key', 'email')
            ->first();

        $this->assertNotNull($emailField);

        $this->actingAs($this->staff, 'admin');
        $this->post(route('clients.detail-verification.accept', ['field' => $emailField->id]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $afterAccept = app(ClientDetailVerificationService::class)->latestSubmittedSummary((int) $this->client->id);
        $this->assertNotNull($afterAccept);
        $this->assertSame('Verification Confirmed and Request Changes', $afterAccept['heading']);
        $this->assertSame('Super1 Admin1', $afterAccept['verified_by']);
    }

    /**
     * @param  array<string, string>|null  $snapshot
     */
    private function createOpenVerification(string $token, ?array $snapshot = null): ClientDetailVerification
    {
        return ClientDetailVerification::query()->create([
            'client_id' => $this->client->id,
            'token_hash' => ClientDetailVerification::hashToken($token),
            'sent_to_email' => $this->client->email,
            'sent_by' => $this->staff->id,
            'snapshot' => $snapshot ?? ClientDetailVerificationFields::buildSnapshot([
                'first_name' => $this->client->first_name,
                'last_name' => $this->client->last_name,
                'primary_email' => $this->client->email,
                'primary_phone' => $this->client->phone,
                'gender' => $this->client->gender,
                'marital_status' => $this->client->marital_status,
            ]),
        ]);
    }

    /**
     * @return list<array<string, string|null>>
     */
    private function confirmedPayload(): array
    {
        return array_map(static fn (string $key): array => [
            'key' => $key,
            'status' => ClientDetailVerificationFields::STATUS_CONFIRMED,
            'requested_value' => null,
            'current_value' => 'N/A',
        ], ClientDetailVerificationFields::keys());
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
                $table->string('gender')->nullable();
                $table->string('marital_status')->nullable();
                $table->string('dob')->nullable();
                $table->unsignedInteger('is_company')->nullable();
                $table->unsignedInteger('is_deleted')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('client_id')->nullable();
                $table->timestamps();
            });
        } else {
            foreach (['gender', 'marital_status', 'dob', 'phone', 'country_code'] as $column) {
                if (! Schema::hasColumn('admins', $column)) {
                    Schema::table('admins', function (Blueprint $table) use ($column) {
                        $table->string($column)->nullable();
                    });
                }
            }
        }

        if (! Schema::hasTable('client_detail_verifications')) {
            Schema::create('client_detail_verifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('client_id')->index();
                $table->string('token_hash', 64)->unique();
                $table->string('sent_to_email', 255);
                $table->unsignedInteger('sent_by')->nullable();
                $table->json('snapshot')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamp('invalidated_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('client_detail_verification_fields')) {
            Schema::create('client_detail_verification_fields', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('verification_id')->index();
                $table->unsignedInteger('client_id')->index();
                $table->string('field_key', 64);
                $table->text('original_value')->nullable();
                $table->text('requested_value')->nullable();
                $table->string('status', 32)->default('confirmed');
                $table->text('note')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->unsignedInteger('accepted_by')->nullable();
                $table->timestamps();
            });
        }
    }

    private function createMattersTable(): void
    {
        if (Schema::hasTable('matters')) {
            return;
        }

        Schema::create('matters', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('title')->nullable();
            $table->string('nick_name')->nullable();
            $table->unsignedInteger('status')->nullable();
        });
    }
}
