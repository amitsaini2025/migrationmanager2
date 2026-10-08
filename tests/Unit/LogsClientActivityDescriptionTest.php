<?php

namespace Tests\Unit;

use App\Traits\LogsClientActivity;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogsClientActivityDescriptionTest extends TestCase
{
    #[Test]
    public function legacy_unset_source_change_renders_as_empty_to_new_value(): void
    {
        $description = $this->harness()->describe([
            'Source' => [
                'old' => null,
                'new' => 'Website',
            ],
        ]);

        $this->assertStringContainsString('(empty)', $description);
        $this->assertStringContainsString('Website', $description);
        $this->assertStringNotContainsString('Others', $description);
    }

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
