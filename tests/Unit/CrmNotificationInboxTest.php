<?php

namespace Tests\Unit;

use App\Models\CheckinLog;
use App\Models\Staff;
use App\Services\OfficeVisitNotificationService;
use App\Support\NotificationInsertCacheBuster;
use App\Support\NotificationUrl;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CrmNotificationInboxTest extends TestCase
{
    public function test_query_parameter_is_appended_without_breaking_an_existing_query_string(): void
    {
        $url = NotificationUrl::withQuery(
            'https://crm.example/notifications/broadcasts/manage?batch=abc-123',
            ['t' => 44]
        );

        $this->assertStringContainsString('batch=abc-123', $url);
        $this->assertStringContainsString('t=44', $url);
        $this->assertStringNotContainsString('?batch=abc-123?t=44', $url);
    }

    public function test_office_visit_message_names_the_client_purpose_and_action(): void
    {
        $visit = new CheckinLog;
        $visit->id = 15;
        $visit->contact_type = 'Walk-in';
        $visit->walk_in_phone = '0400000000';
        $visit->visit_purpose = 'Student visa';
        $visit->status = 0;

        $service = new OfficeVisitNotificationService;

        $this->assertSame(
            'Walk-in (0400000000) — Student visa. Checked in by Reception Melbourne.',
            $service->message($visit, OfficeVisitNotificationService::ACTION_CHECKED_IN, 'Reception Melbourne')
        );
        $this->assertSame(
            'Walk-in (0400000000) — Student visa. Reassigned by Reception Melbourne.',
            $service->message($visit, OfficeVisitNotificationService::ACTION_REASSIGNED, 'Reception Melbourne')
        );
        $this->assertSame(
            'Walk-in (0400000000) — Student visa. Please send. Requested by Reception Melbourne.',
            $service->message($visit, OfficeVisitNotificationService::ACTION_PLEASE_SEND, 'Reception Melbourne')
        );
        $this->assertSame(url('/office-visits/waiting?checkin=15'), $service->visitUrl($visit));
    }

    public function test_only_super_admin_role_sees_every_notification(): void
    {
        $superAdmin = new Staff;
        $superAdmin->role = 1;

        $admin = new Staff;
        $admin->role = 17;

        $agent = new Staff;
        $agent->role = 16;

        $this->assertTrue($superAdmin->seesAllCrmNotifications());
        $this->assertFalse($admin->seesAllCrmNotifications());
        $this->assertFalse($agent->seesAllCrmNotifications());
    }

    public function test_raw_notification_insert_clears_the_receiver_bell_cache(): void
    {
        Cache::put('header:notif_unread:v1:42', 9, 60);

        NotificationInsertCacheBuster::forgetReceivers(
            'insert into "notifications" ("sender_id", "receiver_id", "message") values (?, ?, ?)',
            [1, 42, 'Hello']
        );

        $this->assertNull(Cache::get('header:notif_unread:v1:42'));
    }
}
