<?php

namespace Tests\Unit\Services;

use App\Services\EmailDeliveryStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailDeliveryStatusTest extends TestCase
{
    #[Test]
    public function it_maps_delivery_status_labels(): void
    {
        $this->assertSame('Pending', EmailDeliveryStatus::statusLabel(null));
        $this->assertSame('Delivered', EmailDeliveryStatus::statusLabel('delivered'));
        $this->assertSame('Undelivered', EmailDeliveryStatus::statusLabel('bounced'));
        $this->assertSame('Send Failed', EmailDeliveryStatus::statusLabel('send_failed'));
    }

    #[Test]
    public function it_summarizes_common_user_agents(): void
    {
        $this->assertSame('Apple device', EmailDeliveryStatus::summarizeUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)'));
        $this->assertSame('Gmail', EmailDeliveryStatus::summarizeUserAgent('Mozilla/5.0 Gmail'));
        $this->assertNull(EmailDeliveryStatus::summarizeUserAgent(null));
    }
}
