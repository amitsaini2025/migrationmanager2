<?php

namespace Tests\Unit\Services;

use App\Models\EmailLog;
use App\Models\EmailLogEvent;
use App\Services\SesDeliveryHeaders;
use App\Services\ZohoDelivery;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SentMessage as IlluminateSentMessage;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class ZohoDeliveryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.mailers.smtp.host' => 'email-smtp.ap-southeast-2.amazonaws.com',
            'mail.mailers.zoho.host' => 'smtp.zoho.com',
        ]);

        $this->createSchema();
        EmailLogEvent::query()->delete();
        EmailLog::query()->delete();
    }

    #[Test]
    public function it_recognises_the_zoho_smtp_host_and_not_ses(): void
    {
        $zoho = new ZohoDelivery;

        $this->assertTrue($zoho->isZohoTransport('smtp://smtp.zoho.com:587'));
        $this->assertFalse($zoho->isZohoTransport('smtp://email-smtp.ap-southeast-2.amazonaws.com:587'));
    }

    #[Test]
    public function it_recognises_a_mailbox_zoho_host_unless_it_is_the_ses_host(): void
    {
        config(['mail.mailers.zoho_mailbox_abc.host' => 'smtp.zoho.eu']);

        $zoho = new ZohoDelivery;

        $this->assertTrue($zoho->isZohoTransport('smtp://smtp.zoho.eu:587'));

        config([
            'mail.mailers.smtp.host' => 'smtp.example.com',
            'mail.mailers.zoho_mailbox_abc.host' => 'smtp.example.com',
        ]);

        $this->assertFalse($zoho->isZohoTransport('smtp://smtp.example.com:587'));
    }

    #[Test]
    public function a_zoho_transport_tags_the_message(): void
    {
        $email = $this->email();

        event(new MessageEvent($email, $this->envelope(), 'smtp://smtp.zoho.com:587'));
        event(new MessageEvent($email, $this->envelope(), 'smtp://email-smtp.ap-southeast-2.amazonaws.com:587'));

        $this->assertSame('zoho', $email->getHeaders()->get(ZohoDelivery::VIA_HEADER)->getBodyAsString());
    }

    #[Test]
    public function a_successful_zoho_send_marks_a_pending_log_delivered(): void
    {
        $log = $this->outgoingLog('pending');
        $email = $this->trackedEmail($log->id);
        $email->getHeaders()->addTextHeader(ZohoDelivery::VIA_HEADER, 'zoho');

        event(new MessageSent(new IlluminateSentMessage(new SentMessage($email, $this->envelope()))));

        $log->refresh();
        $this->assertSame('delivered', $log->delivery_status);
        $this->assertNotNull($log->delivered_at);
        $this->assertSame(ZohoDelivery::REASON, $log->status_reason);
        $this->assertSame(1, EmailLogEvent::query()->where('email_log_id', $log->id)->where('event_type', 'delivered')->count());
    }

    #[Test]
    public function a_zoho_send_does_not_replace_a_bounce(): void
    {
        $log = $this->outgoingLog('bounced');
        $email = $this->trackedEmail($log->id);
        $email->getHeaders()->addTextHeader(ZohoDelivery::VIA_HEADER, 'zoho');

        event(new MessageSent(new IlluminateSentMessage(new SentMessage($email, $this->envelope()))));

        $log->refresh();
        $this->assertSame('bounced', $log->delivery_status);
        $this->assertSame(0, EmailLogEvent::query()->count());
    }

    private function email(): Email
    {
        return (new Email)
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Hello')
            ->text('Body');
    }

    private function trackedEmail(int $emailLogId): Email
    {
        $email = $this->email();
        $email->getHeaders()->addTextHeader(SesDeliveryHeaders::LOG_HEADER, (string) $emailLogId);

        return $email;
    }

    private function envelope(): Envelope
    {
        return new Envelope(new Address('from@example.com'), [new Address('to@example.com')]);
    }

    private function outgoingLog(string $status): EmailLog
    {
        return EmailLog::create([
            'from_mail' => 'from@example.com',
            'to_mail' => 'to@example.com',
            'subject' => 'Hello',
            'mail_type' => 1,
            'delivery_status' => $status,
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
                $table->integer('mail_type')->nullable();
                $table->string('delivery_status')->nullable();
                $table->timestamp('delivered_at')->nullable();
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
