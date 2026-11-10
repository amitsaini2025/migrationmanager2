<?php

namespace Tests\Unit\Services;

use App\Services\SesDeliveryHeaders;
use Illuminate\Mail\Events\MessageSending;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class SesDeliveryHeadersTest extends TestCase
{
    #[Test]
    public function it_stamps_the_configuration_set_and_email_log_tag(): void
    {
        config(['services.ses.configuration_set' => 'crm-delivery']);

        $email = new Email;
        (new SesDeliveryHeaders)->apply($email, 42);

        $this->assertSame('42', $email->getHeaders()->get(SesDeliveryHeaders::LOG_HEADER)->getBodyAsString());
        $this->assertSame('crm-delivery', $email->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBodyAsString());
        $this->assertSame('email_log_id=42', $email->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBodyAsString());
    }

    #[Test]
    public function it_skips_the_configuration_set_header_when_none_is_configured(): void
    {
        config(['services.ses.configuration_set' => '']);

        $email = new Email;
        (new SesDeliveryHeaders)->apply($email, 7);

        $this->assertNull($email->getHeaders()->get('X-SES-CONFIGURATION-SET'));
        $this->assertSame('email_log_id=7', $email->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBodyAsString());
    }

    #[Test]
    public function the_sending_listener_adds_ses_headers_for_a_tracked_message(): void
    {
        config(['services.ses.configuration_set' => 'crm-delivery']);

        $email = new Email;
        $email->getHeaders()->addTextHeader(SesDeliveryHeaders::LOG_HEADER, '9');

        event(new MessageSending($email));
        event(new MessageSending($email));

        $this->assertSame('crm-delivery', $email->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBodyAsString());
        $this->assertSame('email_log_id=9', $email->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBodyAsString());
    }
}
