<?php

namespace Tests\Unit\Services;

use App\Models\EmailLog;
use App\Models\EmailLogEvent;
use App\Services\SesEventWebhookService;
use App\Services\SnsMessageVerifier;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SesEventWebhookServiceTest extends TestCase
{
    private const TOPIC = 'arn:aws:sns:ap-southeast-2:123:ses-events';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.ses.sns_topic_arn' => self::TOPIC,
            'services.ses.webhook_token' => 'test-token',
        ]);

        $this->createSchema();
        EmailLogEvent::query()->delete();
        EmailLog::query()->delete();
    }

    #[Test]
    public function a_delivery_notification_marks_the_email_delivered(): void
    {
        $log = $this->outgoingLog();
        $this->allowSignatures();

        $response = $this->postNotification($this->deliveryEvent($log->id));

        $this->assertSame(200, $response['status']);
        $log->refresh();
        $this->assertSame('delivered', $log->delivery_status);
        $this->assertNotNull($log->delivered_at);
        $this->assertSame(1, EmailLogEvent::query()->where('email_log_id', $log->id)->where('event_type', 'delivered')->count());
    }

    #[Test]
    public function a_repeated_delivery_does_not_record_a_second_event(): void
    {
        $log = $this->outgoingLog();
        $this->allowSignatures();
        $event = $this->deliveryEvent($log->id);

        $this->postNotification($event);
        $this->postNotification($event);

        $this->assertSame(1, EmailLogEvent::query()->where('email_log_id', $log->id)->count());
    }

    #[Test]
    public function a_permanent_bounce_marks_the_email_undelivered_and_keeps_the_reason(): void
    {
        $log = $this->outgoingLog();
        $this->allowSignatures();

        $this->postNotification([
            'eventType' => 'Bounce',
            'mail' => [
                'messageId' => 'ses-bounce',
                'timestamp' => '2026-01-01T00:00:00.000Z',
                'destination' => ['to@example.com'],
                'tags' => ['email_log_id' => [(string) $log->id]],
            ],
            'bounce' => [
                'bounceType' => 'Permanent',
                'timestamp' => '2026-01-01T00:00:02.000Z',
                'bouncedRecipients' => [[
                    'emailAddress' => 'to@example.com',
                    'diagnosticCode' => 'smtp; 550 5.1.1 user unknown',
                ]],
            ],
        ]);

        $log->refresh();
        $this->assertSame('bounced', $log->delivery_status);
        $this->assertSame('smtp; 550 5.1.1 user unknown', $log->status_reason);
    }

    #[Test]
    public function a_later_send_event_does_not_downgrade_delivered(): void
    {
        $log = $this->outgoingLog();
        $log->update(['delivery_status' => 'delivered']);
        $this->allowSignatures();

        $this->postNotification([
            'eventType' => 'Send',
            'mail' => [
                'messageId' => 'ses-send',
                'timestamp' => '2026-01-01T00:00:00.000Z',
                'destination' => ['to@example.com'],
                'tags' => ['email_log_id' => [(string) $log->id]],
            ],
        ]);

        $log->refresh();
        $this->assertSame('delivered', $log->delivery_status);
    }

    #[Test]
    public function an_open_records_engagement_and_treats_the_message_as_delivered(): void
    {
        $log = $this->outgoingLog();
        $this->allowSignatures();

        app(SesEventWebhookService::class)->applyEvent([
            'eventType' => 'Open',
            'mail' => [
                'messageId' => 'ses-open',
                'timestamp' => '2026-01-01T00:01:00.000Z',
                'destination' => ['to@example.com'],
                'tags' => ['email_log_id' => [(string) $log->id]],
            ],
            'open' => ['timestamp' => '2026-01-01T00:01:00.000Z'],
        ]);

        $log->refresh();
        $this->assertSame('delivered', $log->delivery_status);
        $this->assertNotNull($log->opened_at);
    }

    #[Test]
    public function a_wrong_token_is_rejected_before_signature_checks(): void
    {
        $verifier = Mockery::mock(SnsMessageVerifier::class);
        $verifier->shouldNotReceive('verify');
        $this->app->instance(SnsMessageVerifier::class, $verifier);

        $request = Request::create('/webhooks/ses/events?token=nope', 'POST', content: '{}');
        $result = app(SesEventWebhookService::class)->handle($request);

        $this->assertSame(401, $result['status']);
    }

    #[Test]
    public function a_subscription_confirmation_is_fetched_from_sns(): void
    {
        Http::fake(['https://sns.ap-southeast-2.amazonaws.com/*' => Http::response('confirmed', 200)]);
        $verifier = Mockery::mock(SnsMessageVerifier::class);
        $verifier->shouldReceive('verify')->once();
        $verifier->shouldReceive('isAllowedSnsUrl')->once()->andReturn(true);
        $this->app->instance(SnsMessageVerifier::class, $verifier);

        $request = Request::create('/webhooks/ses/events?token=test-token', 'POST', content: json_encode([
            'Type' => 'SubscriptionConfirmation',
            'TopicArn' => self::TOPIC,
            'SubscribeURL' => 'https://sns.ap-southeast-2.amazonaws.com/?Action=ConfirmSubscription',
            'Message' => 'confirm',
            'MessageId' => 'mid',
            'Timestamp' => '2026-01-01T00:00:00.000Z',
            'Token' => 'token',
            'Signature' => 'abc',
            'SigningCertURL' => 'https://sns.ap-southeast-2.amazonaws.com/cert.pem',
        ], JSON_THROW_ON_ERROR));

        $result = app(SesEventWebhookService::class)->handle($request);

        $this->assertSame(200, $result['status']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'ConfirmSubscription'));
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array{body: string, status: int}
     */
    private function postNotification(array $event): array
    {
        $request = Request::create('/webhooks/ses/events?token=test-token', 'POST', content: json_encode([
            'Type' => 'Notification',
            'TopicArn' => self::TOPIC,
            'Message' => json_encode($event),
            'MessageId' => 'mid-'.md5(json_encode($event)),
            'Timestamp' => '2026-01-01T00:00:00.000Z',
            'Signature' => 'abc',
            'SigningCertURL' => 'https://sns.ap-southeast-2.amazonaws.com/cert.pem',
            'SignatureVersion' => '1',
        ], JSON_THROW_ON_ERROR));

        return app(SesEventWebhookService::class)->handle($request);
    }

    /**
     * @return array<string, mixed>
     */
    private function deliveryEvent(int $emailLogId): array
    {
        return [
            'eventType' => 'Delivery',
            'mail' => [
                'messageId' => 'ses-delivered',
                'timestamp' => '2026-01-01T00:00:00.000Z',
                'destination' => ['to@example.com'],
                'tags' => ['email_log_id' => [(string) $emailLogId]],
            ],
            'delivery' => [
                'timestamp' => '2026-01-01T00:00:05.000Z',
                'smtpResponse' => '250 ok',
            ],
        ];
    }

    private function allowSignatures(): void
    {
        $verifier = Mockery::mock(SnsMessageVerifier::class);
        $verifier->shouldReceive('verify');
        $this->app->instance(SnsMessageVerifier::class, $verifier);
    }

    private function outgoingLog(): EmailLog
    {
        return EmailLog::create([
            'from_mail' => 'from@example.com',
            'to_mail' => 'to@example.com',
            'subject' => 'Hello',
            'mail_type' => 1,
            'delivery_status' => 'pending',
        ]);
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
                $table->string('from_mail')->nullable();
                $table->string('to_mail')->nullable();
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->integer('mail_type')->nullable();
                $table->string('delivery_status')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('clicked_at')->nullable();
                $table->timestamp('spam_reported_at')->nullable();
                $table->text('status_reason')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('email_log_events')) {
            Schema::create('email_log_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('email_log_id');
                $table->string('event_type', 32);
                $table->timestamp('occurred_at');
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }
    }
}
