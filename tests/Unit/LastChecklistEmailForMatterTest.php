<?php

namespace Tests\Unit;

use App\Models\EmailLog;
use App\Services\MatterEmailBodyCleanupService;
use App\Support\LastChecklistEmailForMatter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LastChecklistEmailForMatterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('email_logs');
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('client_matter_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('subject')->nullable();
            $table->longText('message')->nullable();
            $table->text('attachments')->nullable();
            $table->string('mail_body_type')->nullable();
            $table->integer('mail_type')->nullable();
            $table->timestamps();
        });
    }

    #[Test]
    public function prefers_latest_checklist_style_sent_email_for_matter(): void
    {
        EmailLog::query()->create([
            'client_matter_id' => 10,
            'template_id' => 5,
            'subject' => 'Older checklist',
            'message' => '<p>Old body</p>',
            'attachments' => json_encode([['file_name' => 'a.pdf', 'file_url' => 'x']]),
            'mail_body_type' => 'sent',
            'mail_type' => 1,
        ]);

        $latest = EmailLog::query()->create([
            'client_matter_id' => 10,
            'template_id' => 5,
            'subject' => 'Checklist & Payment Request',
            'message' => '<p>Latest checklist with <a href="https://example.com/sign/1/abc">Sign</a></p>',
            'attachments' => json_encode([['file_name' => 'checklist.pdf', 'file_url' => 'y']]),
            'mail_body_type' => 'sent',
            'mail_type' => 1,
        ]);

        EmailLog::query()->create([
            'client_matter_id' => 99,
            'subject' => 'Other matter',
            'message' => '<p>Wrong matter</p>',
            'attachments' => json_encode([['file_name' => 'z.pdf', 'file_url' => 'z']]),
            'mail_body_type' => 'sent',
            'mail_type' => 1,
        ]);

        $result = $this->resolver()->find(10, 5);

        $this->assertNotNull($result);
        $this->assertSame((int) $latest->id, $result['id']);
        $this->assertSame('Checklist & Payment Request', $result['subject']);
        $this->assertStringContainsString('Sign', $result['message']);
        $this->assertStringContainsString('/sign/1/abc', $result['message']);
    }

    #[Test]
    public function returns_null_when_matter_has_no_sent_email_body(): void
    {
        $this->assertNull($this->resolver()->find(10, 5));
    }

    #[Test]
    public function sheet_popup_prefers_last_checklist_email_over_first_template(): void
    {
        $sheet = file_get_contents(resource_path('views/crm/clients/sheets/visa-type-sheet.blade.php'));
        $this->assertIsString($sheet);
        $this->assertStringContainsString('last_checklist_email', $sheet);
        $this->assertStringContainsString('Prefer last sent checklist email', $sheet);
    }

    private function resolver(): LastChecklistEmailForMatter
    {
        return new LastChecklistEmailForMatter(app(MatterEmailBodyCleanupService::class));
    }
}
