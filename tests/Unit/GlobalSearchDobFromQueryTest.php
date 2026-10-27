<?php

namespace Tests\Unit;

use App\Http\Controllers\CRM\ClientsController;
use PHPUnit\Framework\Assert;
use ReflectionMethod;
use Tests\TestCase;

class GlobalSearchDobFromQueryTest extends TestCase
{
    public function test_full_dd_mm_yyyy_is_converted_to_iso_for_dob_match(): void
    {
        Assert::assertSame('1990-08-26', $this->dobFromQuery('26/08/1990'));
    }

    public function test_partial_date_does_not_throw_and_skips_dob_match(): void
    {
        Assert::assertSame('', $this->dobFromQuery('26/08'));
        Assert::assertSame('', $this->dobFromQuery('12/08'));
    }

    public function test_two_digit_year_skips_dob_match(): void
    {
        Assert::assertSame('', $this->dobFromQuery('07/05/19'));
        Assert::assertSame('', $this->dobFromQuery('26/08/90'));
    }

    public function test_invalid_calendar_date_skips_dob_match(): void
    {
        Assert::assertSame('', $this->dobFromQuery('32/08/1990'));
        Assert::assertSame('', $this->dobFromQuery('15/13/1990'));
    }

    public function test_non_date_slash_queries_skip_dob_match(): void
    {
        Assert::assertSame('', $this->dobFromQuery('VIPL/123'));
        Assert::assertSame('', $this->dobFromQuery('foo/bar'));
        Assert::assertSame('', $this->dobFromQuery('john smith'));
    }

    public function test_extra_slash_segments_still_use_first_three_parts(): void
    {
        Assert::assertSame('1990-08-26', $this->dobFromQuery('26/08/1990/extra'));
    }

    private function dobFromQuery(string $squery): string
    {
        $method = new ReflectionMethod(ClientsController::class, 'globalSearchDobFromQuery');
        $method->setAccessible(true);

        /** @var string $dob */
        $dob = $method->invoke($this->app->make(ClientsController::class), $squery);

        return $dob;
    }
}
