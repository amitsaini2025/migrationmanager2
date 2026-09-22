<?php

namespace Tests\Unit\Support;

use App\Support\NoteDescriptionHtml;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class NoteDescriptionHtmlTest extends TestCase
{
    public function test_short_html_is_sent_in_full_and_not_truncated(): void
    {
        $feed = NoteDescriptionHtml::forFeedList('<p>Called the client</p>', 'note');

        Assert::assertFalse($feed['message_truncated']);
        Assert::assertStringContainsString('Called the client', $feed['message']);
        Assert::assertStringContainsString('<p>', $feed['message']);
    }

    public function test_oversized_html_is_previewed_without_the_full_blob(): void
    {
        $body = str_repeat('Pasted email body. ', 800);
        Assert::assertGreaterThan(NoteDescriptionHtml::FEED_LIST_MAX_BYTES, strlen($body));

        $feed = NoteDescriptionHtml::forFeedList('<div>'.$body.'</div>', 'note');

        Assert::assertTrue($feed['message_truncated']);
        Assert::assertLessThan(NoteDescriptionHtml::FEED_LIST_MAX_BYTES, strlen($feed['message']));
        Assert::assertStringNotContainsString('<div>', $feed['message']);
        Assert::assertStringEndsWith('…', html_entity_decode($feed['message'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public function test_appointment_html_is_never_truncated(): void
    {
        $html = '<div class="appointment-activity-detail">'.str_repeat('x', 12000).'</div>';

        $feed = NoteDescriptionHtml::forFeedList($html, 'activity');

        Assert::assertFalse($feed['message_truncated']);
        Assert::assertStringContainsString('appointment-activity-detail', $feed['message']);
    }

    public function test_stage_html_is_never_truncated(): void
    {
        $html = '<p>'.str_repeat('Moved to next stage. ', 700).'</p>';

        $feed = NoteDescriptionHtml::forFeedList($html, 'stage');

        Assert::assertFalse($feed['message_truncated']);
        Assert::assertStringContainsString('Moved to next stage', $feed['message']);
    }

    public function test_empty_description_is_not_truncated(): void
    {
        Assert::assertSame([
            'message' => '',
            'message_truncated' => false,
        ], NoteDescriptionHtml::forFeedList('', 'note'));
    }

    public function test_list_and_expand_wires_stay_in_place(): void
    {
        $trait = (string) file_get_contents(app_path('Traits/ClientCrmFollowups.php'));
        $routes = (string) file_get_contents(base_path('routes/clients.php'));
        $feedJs = (string) file_get_contents(public_path('js/crm/clients/tabs/activity-feed.js'));
        $clientDetail = (string) file_get_contents(resource_path('views/crm/clients/detail.blade.php'));
        $companyDetail = (string) file_get_contents(resource_path('views/crm/companies/detail.blade.php'));

        Assert::assertStringContainsString('NoteDescriptionHtml::forFeedList', $trait);
        Assert::assertStringContainsString('function activityMessage', $trait);
        Assert::assertStringContainsString("name('clients.activityMessage')", $routes);
        Assert::assertStringContainsString('feed-item-show-more', $feedJs);
        Assert::assertStringContainsString('getActivityMessage', $clientDetail);
        Assert::assertStringContainsString('getActivityMessage', $companyDetail);
        Assert::assertStringContainsString('v.message_truncated', $clientDetail);
        Assert::assertStringContainsString('v.message_truncated', $companyDetail);
    }
}
