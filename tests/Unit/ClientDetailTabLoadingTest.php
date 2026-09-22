<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class ClientDetailTabLoadingTest extends TestCase
{
    public function test_client_detail_tab_loading_component_renders_spinner_and_label(): void
    {
        $html = Blade::render('<x-client-detail-tab-loading label="Loading emails…" data-emails-lazy-placeholder />');

        Assert::assertStringContainsString('client-detail-tab-loading', $html);
        Assert::assertStringContainsString('client-detail-tab-loading__label', $html);
        Assert::assertStringContainsString('Loading emails', $html);
        Assert::assertStringContainsString('data-emails-lazy-placeholder', $html);
        Assert::assertMatchesRegularExpression('/fa-spinner|icon-spin/', $html);
    }
}
