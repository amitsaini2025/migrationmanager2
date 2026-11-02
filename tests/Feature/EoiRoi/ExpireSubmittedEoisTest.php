<?php

namespace Tests\Feature\EoiRoi;

use App\Models\ClientEoiReference;
use Tests\TestCase;

/**
 * Command behaviour is covered in Tests\Unit\ExpireSubmittedEoisServiceTest
 * with an isolated sqlite schema (no RefreshDatabase).
 */
class ExpireSubmittedEoisTest extends TestCase
{
    public function test_statuses_include_expired(): void
    {
        $this->assertContains('expired', ClientEoiReference::statuses());
        $this->assertContains('submitted', ClientEoiReference::statuses());
    }
}
