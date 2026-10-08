<?php

namespace Tests\Unit;

use App\Traits\LogsClientActivity;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogsClientActivityDescriptionTest extends TestCase
{
    #[Test]
    public function detailed_changes_with_null_new_value_do_not_crash(): void
    {
        $description = $this->harness()->describe([
            'Source' => [
                'old' => 'Others',
                'new' => null,
            ],
        ]);

        $this->assertStringContainsString('Source:', $description);
        $this->assertStringContainsString('(empty)', $description);
    }

    #[Test]
    public function simple_field_name_list_uses_values_not_numeric_keys(): void
    {
        $description = $this->harness()->describe([
            'first_name' => 'First Name',
            'source' => 'Source',
        ]);

        $this->assertStringContainsString('First Name', $description);
        $this->assertStringContainsString('Source', $description);
    }

    private function harness(): object
    {
        return new class
        {
            use LogsClientActivity;

            public function describe(array $changedFields): string
            {
                return $this->buildChangedFieldsDescription($changedFields);
            }
        };
    }
}
