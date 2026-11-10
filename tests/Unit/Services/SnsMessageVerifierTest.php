<?php

namespace Tests\Unit\Services;

use App\Services\SnsMessageVerifier;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class SnsMessageVerifierTest extends TestCase
{
    #[Test]
    public function it_builds_the_notification_string_to_sign_with_subject_in_aws_order(): void
    {
        $signed = (new SnsMessageVerifier)->stringToSign([
            'Type' => 'Notification',
            'MessageId' => 'mid',
            'TopicArn' => 'arn:aws:sns:ap-southeast-2:1:ses',
            'Subject' => 'Amazon SES Email Event Notification',
            'Message' => '{"eventType":"Delivery"}',
            'Timestamp' => '2026-01-01T00:00:00.000Z',
        ]);

        $this->assertSame(
            "Message\n{\"eventType\":\"Delivery\"}\nMessageId\nmid\nSubject\nAmazon SES Email Event Notification\nTimestamp\n2026-01-01T00:00:00.000Z\nTopicArn\narn:aws:sns:ap-southeast-2:1:ses\nType\nNotification\n",
            $signed
        );
    }

    #[Test]
    public function it_only_trusts_https_sns_certificate_urls(): void
    {
        $verifier = new SnsMessageVerifier;

        $this->assertTrue($verifier->isAllowedCertUrl('https://sns.ap-southeast-2.amazonaws.com/SimpleNotificationService.pem'));
        $this->assertFalse($verifier->isAllowedCertUrl('http://sns.ap-southeast-2.amazonaws.com/SimpleNotificationService.pem'));
        $this->assertFalse($verifier->isAllowedCertUrl('https://evil.example/sns.amazonaws.com/cert.pem'));
        $this->assertFalse($verifier->isAllowedSnsUrl('https://sns.ap-southeast-2.amazonaws.com.evil.com/confirm'));
    }

    #[Test]
    public function it_rejects_an_unsupported_message_type(): void
    {
        $this->expectException(RuntimeException::class);

        (new SnsMessageVerifier)->stringToSign(['Type' => 'SomethingElse']);
    }
}
