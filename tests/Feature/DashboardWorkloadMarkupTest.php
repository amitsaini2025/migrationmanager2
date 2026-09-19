<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardWorkloadMarkupTest extends TestCase
{
    public function test_dashboard_includes_workload_strip_instead_of_legacy_kpi_cards(): void
    {
        $blade = file_get_contents(resource_path('views/crm/dashboard-optimized.blade.php'));
        $this->assertNotFalse($blade);

        $this->assertStringContainsString('x-dashboard.workload-strip', $blade);
        $this->assertStringContainsString('x-dashboard.my-day', $blade);

        $strip = file_get_contents(resource_path('views/components/dashboard/workload-strip.blade.php'));
        $this->assertNotFalse($strip);
        $this->assertStringContainsString('workload-queue-bar', $strip);
        $this->assertStringContainsString('workload-chip--queue', $strip);
        $this->assertStringContainsString('workload-chip--done', $strip);
        $this->assertStringContainsString('workload-chip--updates', $strip);
        $this->assertStringContainsString('data-workload-metric="pending"', $strip);
        $this->assertStringContainsString('data-workload-metric="completed_excl_call"', $strip);
        $this->assertStringContainsString('data-workload-metric="call_completed"', $strip);
        $this->assertStringContainsString('data-workload-metric="updated"', $strip);
        $this->assertStringContainsString('role="button"', $strip);
        $this->assertStringNotContainsString('role="list"', $strip);
        $this->assertStringNotContainsString('Call list', $strip);
        $this->assertStringNotContainsString('call notes today', $strip);
        $this->assertStringNotContainsString('in-person today', $strip);
        $this->assertStringNotContainsString('x-dashboard.workload-card', $strip);
        $this->assertStringNotContainsString('New = record created', $strip);
        $this->assertStringNotContainsString('workload-legend', $strip);
        $this->assertStringNotContainsString('Queue = open assigned', $strip);

        $myDay = file_get_contents(resource_path('views/components/dashboard/my-day.blade.php'));
        $this->assertNotFalse($myDay);
        $this->assertStringNotContainsString('my-day-sub', $myDay);
        $this->assertStringNotContainsString('Queue above stays CRM-only', $myDay);
        $this->assertStringNotContainsString('Copy for Teams instead of rewriting', $myDay);
        $this->assertStringContainsString('x-dashboard.file-time-auto', $myDay);
        $this->assertStringContainsString('x-dashboard.files-opened', $myDay);
        $this->assertStringContainsString('x-dashboard.activity-counts', $myDay);
        $splitPos = strpos($myDay, 'x-dashboard.files-opened');
        $countsPos = strpos($myDay, 'x-dashboard.activity-counts');
        $eodPos = strpos($myDay, 'id="myDayEodSection"');
        $this->assertNotFalse($splitPos);
        $this->assertNotFalse($countsPos);
        $this->assertNotFalse($eodPos);
        $this->assertTrue($splitPos < $countsPos && $countsPos < $eodPos);

        $activityCounts = file_get_contents(resource_path('views/components/dashboard/activity-counts.blade.php'));
        $this->assertNotFalse($activityCounts);
        $this->assertStringContainsString('myDayCountChecklists', $activityCounts);
        $this->assertStringContainsString('myDayCountDocuments', $activityCounts);
        $this->assertStringContainsString('myDayCountActions', $activityCounts);
        $this->assertStringContainsString('myDayCountSms', $activityCounts);
        $this->assertLessThan(
            strpos($activityCounts, 'myDayCountChecklists'),
            strpos($activityCounts, 'Checklists sent')
        );

        $fileTimeAuto = file_get_contents(resource_path('views/components/dashboard/file-time-auto.blade.php'));
        $this->assertNotFalse($fileTimeAuto);
        $this->assertStringContainsString("row['url']", $fileTimeAuto);
        $this->assertStringContainsString('myDayAutoCount', $fileTimeAuto);
        $this->assertStringContainsString('my-day-opened-badge', $fileTimeAuto);
        $this->assertStringContainsString('my-day-auto-events-btn', $fileTimeAuto);
        $this->assertStringContainsString('data-total-minutes', $fileTimeAuto);
        $this->assertStringContainsString('data-shows-avg', $fileTimeAuto);

        $crmEvents = file_get_contents(resource_path('views/components/dashboard/crm-events.blade.php'));
        $this->assertNotFalse($crmEvents);
        $this->assertStringContainsString('my-day-crm-meta', $crmEvents);
        $this->assertStringContainsString('my-day-mins-chip', $crmEvents);
        $this->assertStringContainsString("item['minutes']", $crmEvents);
        $this->assertStringContainsString("item['url']", $crmEvents);
        $this->assertStringContainsString('my-day-crm-show-more', $crmEvents);
        $this->assertStringContainsString("item['body']", $crmEvents);
        $this->assertStringContainsString('is-collapsed', $crmEvents);
        $this->assertStringContainsString('my-day-more-btn', $crmEvents);
        $this->assertStringContainsString('data-total', $crmEvents);
        $this->assertStringContainsString('myDayAddBtn', $myDay);
        $this->assertStringContainsString('myDayAddBtnEod', $myDay);
        $this->assertStringContainsString('myDayLogModal', $myDay);
        $this->assertStringContainsString('myDayAutoEventsModal', $myDay);
        $this->assertStringContainsString('myDayManualList', $myDay);
        $this->assertStringContainsString('myDayEod', $myDay);
        $this->assertStringNotContainsString('myDayEodMoreBtn', $myDay);
        $this->assertStringContainsString('myDayEodToggle', $myDay);
        $this->assertStringContainsString('myDayEodBody', $myDay);
        $this->assertStringContainsString('myDayStillOpenList', $myDay);
        $this->assertStringContainsString('is-collapsed', $myDay);
        $this->assertStringContainsString('aria-expanded="false"', $myDay);
        $this->assertStringNotContainsString('x-dashboard.file-time-board', $myDay);
        $this->assertStringNotContainsString('x-dashboard.file-time-capture', $myDay);
        $this->assertStringNotContainsString('In progress', $myDay);
        $this->assertStringNotContainsString('Parked', $myDay);
        $this->assertStringNotContainsString('Done today', $myDay);
        $this->assertStringContainsString('workloadDrilldownModal', $blade);
        $this->assertStringNotContainsString('Active Matters', $blade);
        $this->assertStringNotContainsString('Urgent Notes Deadlines', $blade);
        $this->assertStringNotContainsString('x-dashboard.kpi-card', $blade);
    }
}
