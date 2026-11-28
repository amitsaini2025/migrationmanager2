<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\CRMUtilityController;
use App\Services\CrmSentEmailS3Service;
use App\Services\EmailService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class SendChecklistIntentFlagTest extends TestCase
{
    #[Test]
    #[DataProvider('truthyFlagProvider')]
    public function request_flag_is_truthy_matches_compose_hidden_values(mixed $value, bool $expected): void
    {
        $controller = new CRMUtilityController(
            $this->createMock(EmailService::class),
            $this->createMock(CrmSentEmailS3Service::class),
        );

        $method = new ReflectionMethod(CRMUtilityController::class, 'requestFlagIsTruthy');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($controller, $value));
    }

    /**
     * @return array<string, array{0: mixed, 1: bool}>
     */
    public static function truthyFlagProvider(): array
    {
        return [
            'string one' => ['1', true],
            'string zero' => ['0', false],
            'empty string' => ['', false],
            'true string' => ['true', true],
            'yes string' => ['yes', true],
            'on string' => ['on', true],
            'bool true' => [true, true],
            'bool false' => [false, false],
            'int one' => [1, true],
            'int zero' => [0, false],
            'null' => [null, false],
        ];
    }
}
