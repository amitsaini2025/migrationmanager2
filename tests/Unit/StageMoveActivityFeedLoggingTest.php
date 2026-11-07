<?php

namespace Tests\Unit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StageMoveActivityFeedLoggingTest extends TestCase
{
    #[Test]
    public function next_and_previous_stage_always_write_activity_feed_even_from_client_portal(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/ClientPortalController.php'));
        Assert::assertNotFalse($controller);

        $nextBody = $this->methodBody($controller, 'updateClientMatterNextStage');
        $prevBody = $this->methodBody($controller, 'updateClientMatterPreviousStage');

        Assert::assertStringContainsString("activity_type = 'stage'", $nextBody);
        Assert::assertStringContainsString('moved the stage from', $nextBody);
        Assert::assertStringNotContainsString(
            'shouldOmitActivitiesLogForClientPortalWebContext($request)',
            $nextBody,
            'Next-stage Activity Feed logging must not be omitted for Client Portal context.'
        );

        Assert::assertStringContainsString("activity_type = 'stage'", $prevBody);
        Assert::assertStringContainsString('moved the stage from', $prevBody);
        Assert::assertStringNotContainsString(
            'shouldOmitActivitiesLogForClientPortalWebContext($request)',
            $prevBody,
            'Previous-stage Activity Feed logging must not be omitted for Client Portal context.'
        );

        // Client Portal Action notes remain for portal-sourced moves (separate from Activity Feed).
        Assert::assertStringContainsString("input('source') === 'client_portal'", $nextBody);
        Assert::assertStringContainsString('createClientPortalAction', $nextBody);
        Assert::assertStringContainsString("input('source') === 'client_portal'", $prevBody);
        Assert::assertStringContainsString('createClientPortalAction', $prevBody);
    }

    #[Test]
    public function other_client_portal_actions_still_omit_activity_feed_via_helper(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/ClientPortalController.php'));
        Assert::assertNotFalse($controller);

        Assert::assertStringContainsString('function shouldOmitActivitiesLogForClientPortalWebContext', $controller);

        $discontinueBody = $this->methodBody($controller, 'discontinueClientMatter');
        Assert::assertStringContainsString('shouldOmitActivitiesLogForClientPortalWebContext', $discontinueBody);

        $reopenBody = $this->methodBody($controller, 'reopenClientMatter');
        Assert::assertStringContainsString('shouldOmitActivitiesLogForClientPortalWebContext', $reopenBody);
    }

    #[Test]
    public function document_sign_auto_advance_writes_stage_activity_feed_row(): void
    {
        $finalizer = file_get_contents($this->projectPath('app/Services/SignedDocumentFinalizer.php'));
        Assert::assertNotFalse($finalizer);

        Assert::assertStringContainsString('ActivitiesLog::create([', $finalizer);
        Assert::assertStringContainsString("'activity_type' => 'stage'", $finalizer);
        Assert::assertStringContainsString("'source' => 'document_sign'", $finalizer);
        Assert::assertStringContainsString('moved the stage from <b>', $finalizer);
    }

    private function methodBody(string $source, string $methodName): string
    {
        $pattern = '/function\s+'.preg_quote($methodName, '/').'\s*\([^)]*\)\s*\{/';
        Assert::assertSame(1, preg_match($pattern, $source, $matches, PREG_OFFSET_CAPTURE), "Method {$methodName} not found");

        $start = $matches[0][1] + strlen($matches[0][0]) - 1; // position of opening '{'
        $depth = 0;
        $length = strlen($source);

        for ($i = $start; $i < $length; $i++) {
            $char = $source[$i];
            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($source, $start, $i - $start + 1);
                }
            }
        }

        Assert::fail("Could not extract body for {$methodName}");
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
