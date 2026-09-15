<?php

namespace Tests\Unit;

use App\Events\OfficeVisitNotificationCreated;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class OfficeVisitNotificationCreatedAtFormatTest extends TestCase
{
    public function test_office_visit_notification_payloads_use_iso8601_created_at(): void
    {
        $sources = [
            base_path('app/Http/Controllers/CRM/OfficeVisitController.php'),
            base_path('app/Http/Controllers/CRM/CRMUtilityController.php'),
            base_path('app/Services/DashboardService.php'),
            base_path('app/Services/FrontDesk/CheckInNotificationService.php'),
        ];

        foreach ($sources as $path) {
            $contents = file_get_contents($path);
            Assert::assertNotFalse($contents, "Failed to read [{$path}].");

            Assert::assertMatchesRegularExpression(
                "/['\"]created_at['\"]\s*=>\s*.*toIso8601String\(\)/",
                $contents,
                "Expected created_at ISO8601 formatting in [{$path}]."
            );
            Assert::assertDoesNotMatchRegularExpression(
                "/['\"]created_at['\"]\s*=>\s*.*format\(['\"]d\/m\/Y h:i A['\"]\)/",
                $contents,
                "Legacy d/m/Y created_at must not remain in [{$path}]."
            );
        }
    }

    public function test_office_visit_popup_falls_back_when_formatter_returns_empty(): void
    {
        $layouts = [
            resource_path('views/layouts/crm_client_detail_dashboard.blade.php'),
            resource_path('views/layouts/crm_client_detail.blade.php'),
        ];

        foreach ($layouts as $path) {
            $contents = file_get_contents($path);
            Assert::assertNotFalse($contents, "Failed to read [{$path}].");
            Assert::assertStringContainsString(
                'formatDisplayDateTime(notification.created_at) || notification.created_at || \'\'',
                $contents,
                "Office visit Time field should fall back to raw created_at in [{$path}]."
            );
        }
    }

    public function test_office_visit_broadcast_event_passes_created_at_through(): void
    {
        $iso = '2026-09-15T12:25:00+10:00';

        $payload = (new OfficeVisitNotificationCreated(
            42,
            7,
            [
                'id' => 42,
                'checkin_id' => 9,
                'created_at' => $iso,
            ]
        ))->broadcastWith();

        Assert::assertSame($iso, $payload['notification']['created_at']);
        Assert::assertNotEmpty($payload['timestamp']);
    }

    public function test_format_display_datetime_parses_iso_and_legacy_au_slash_dates(): void
    {
        $jsPath = str_replace('\\', '/', base_path('public/js/datetime-display.js'));
        $script = 'process.env.TZ = "Australia/Sydney";'
            .'const fs = require("fs");'
            .'const vm = require("vm");'
            .'const ctx = { window: {}, Date, String, parseInt, isFinite, isNaN };'
            .'vm.createContext(ctx);'
            .'vm.runInContext(fs.readFileSync('.json_encode($jsPath, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES).', "utf8"), ctx);'
            .'const format = ctx.window.formatDisplayDateTime;'
            .'const iso = format("2026-09-15T12:25:00+10:00");'
            .'const legacy = format("15/09/2026 12:25 PM");'
            .'const legacy24 = format("15/09/2026 14:30");'
            .'const dateOnly = format("15/09/2026");'
            .'const invalid = format("not-a-date");'
            .'const rolled = format("31/02/2026 12:25 PM");'
            .'const badMinute = format("15/09/2026 12:99");'
            .'console.log(JSON.stringify({ iso: iso, legacy: legacy, legacy24: legacy24, dateOnly: dateOnly, invalid: invalid, rolled: rolled, badMinute: badMinute }));';

        $result = $this->runNodeScript($script);
        $decoded = json_decode($result, true);

        Assert::assertIsArray($decoded);
        // ISO with an offset is converted to the runtime local zone; only the calendar day is stable here.
        Assert::assertMatchesRegularExpression('/^\d{1,2} Sep 2026, \d{1,2}:\d{2} (am|pm)$/', $decoded['iso']);
        Assert::assertSame('15 Sep 2026, 12:25 pm', $decoded['legacy']);
        Assert::assertSame('15 Sep 2026, 2:30 pm', $decoded['legacy24']);
        Assert::assertSame('15 Sep 2026, 12:00 am', $decoded['dateOnly']);
        Assert::assertSame('', $decoded['invalid']);
        Assert::assertSame('', $decoded['rolled']);
        Assert::assertSame('', $decoded['badMinute']);
    }

    public function test_other_format_display_datetime_json_payloads_use_iso8601(): void
    {
        $eoi = file_get_contents(base_path('app/Http/Controllers/CRM/ClientEoiRoiController.php'));
        Assert::assertNotFalse($eoi);
        Assert::assertStringContainsString("email_sent_at' => \$eoi->confirmation_email_sent_at?->toIso8601String()", $eoi);
        Assert::assertStringNotContainsString("email_sent_at' => \$eoi->confirmation_email_sent_at?->format('d/m/Y H:i')", $eoi);
        Assert::assertStringContainsString("created_at' => \$doc->created_at?->toIso8601String()", $eoi);

        $docs = file_get_contents(base_path('app/Http/Controllers/CRM/Clients/ClientDocumentsController.php'));
        Assert::assertNotFalse($docs);
        Assert::assertStringContainsString("uploaded_at'] = \$obj->created_at?->toIso8601String()", $docs);
        Assert::assertStringNotContainsString("uploaded_at'] = \$obj->created_at ? \$obj->created_at->format('d/m/Y H:i')", $docs);

        $eoiJs = file_get_contents(base_path('public/js/clients/eoi-roi.js'));
        Assert::assertNotFalse($eoiJs);
        Assert::assertStringContainsString(
            'formatDisplayDateTime(eoi.email_sent_at) || eoi.email_sent_at',
            $eoiJs
        );
    }

    private function runNodeScript(string $script): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'dtjs');
        Assert::assertNotFalse($tmp);
        file_put_contents($tmp, $script);

        $command = 'node '.escapeshellarg($tmp).' 2>&1';
        $output = shell_exec($command);
        @unlink($tmp);

        Assert::assertIsString($output, 'Node script produced no output.');
        $line = trim($output);
        Assert::assertNotSame('', $line, "Node script output was empty: {$output}");

        return $line;
    }
}
