<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\CRMUtilityController;
use App\Services\CrmSentEmailS3Service;
use App\Services\EmailService;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class RecordChecklistEmailReminderNullMatterTest extends TestCase
{
    #[Test]
    public function null_client_matter_id_does_not_throw_when_checklist_email_reminder_requested(): void
    {
        $controller = new CRMUtilityController(
            $this->createStub(EmailService::class),
            $this->createStub(CrmSentEmailS3Service::class)
        );

        $method = new ReflectionMethod(CRMUtilityController::class, 'recordChecklistEmailReminderIfRequested');
        $method->setAccessible(true);

        $method->invoke($controller, [
            'checklist_reminder_type' => 'email',
        ], null, false);

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function integer_client_matter_id_without_checklist_flag_still_noops(): void
    {
        $controller = new CRMUtilityController(
            $this->createStub(EmailService::class),
            $this->createStub(CrmSentEmailS3Service::class)
        );

        $method = new ReflectionMethod(CRMUtilityController::class, 'recordChecklistEmailReminderIfRequested');
        $method->setAccessible(true);

        $method->invoke($controller, [], 42, false);

        $this->addToAssertionCount(1);
    }
}
