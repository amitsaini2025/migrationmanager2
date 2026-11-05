<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailTemplateLogoTest extends TestCase
{
    #[Test]
    public function receipt_email_header_uses_the_existing_logo_file(): void
    {
        $template = (string) file_get_contents(resource_path('views/emails/template.blade.php'));

        $this->assertStringContainsString("public_path('img/logo.png')", $template);
        $this->assertStringContainsString('emails.partials.inline-logo', $template);
        $this->assertStringContainsString('alt="Bansal Immigration Consultants"', $template);
        $this->assertStringNotContainsString('bansal-imm-logo-11_vrUFM77pu7.png', $template);
        $this->assertFileExists(public_path('img/logo.png'));
    }
}
