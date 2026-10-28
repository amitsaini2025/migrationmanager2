<?php

namespace Tests\Unit;

use App\Support\ArtMatterMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ArtSheetMatterMatchTest extends TestCase
{
    #[Test]
    #[DataProvider('artMatterProvider')]
    public function matches_real_art_matters(?string $nick, ?string $title): void
    {
        $this->assertTrue(ArtMatterMatcher::matches($nick, $title));
    }

    #[Test]
    #[DataProvider('nonArtMatterProvider')]
    public function rejects_partner_and_other_false_positives(?string $nick, ?string $title): void
    {
        $this->assertFalse(ArtMatterMatcher::matches($nick, $title));
    }

    #[Test]
    public function sql_condition_uses_whole_word_art_not_substring_like(): void
    {
        $sql = ArtMatterMatcher::sqlCondition('m');

        $this->assertStringContainsString("LOWER(COALESCE(m.nick_name, '')) = 'art'", $sql);
        $this->assertStringContainsString("~ '\\mart\\M'", $sql);
        $this->assertStringContainsString('%administrative appeals%', $sql);
        $this->assertStringContainsString('%tribunal%', $sql);
        $this->assertStringNotContainsString("LIKE '%art%'", $sql);
    }

    /**
     * @return array<string, array{0: ?string, 1: ?string}>
     */
    public static function artMatterProvider(): array
    {
        return [
            'nick art' => ['art', 'Something else'],
            'nick ART case' => ['ART', 'Partner related note'],
            'title ART alone' => [null, 'ART'],
            'title ART appeal' => ['pv', 'ART Appeal'],
            'title with ART word' => ['', 'Client ART hearing prep'],
            'administrative appeals' => [null, 'Administrative Appeals Tribunal'],
            'tribunal marker' => ['other', 'Migration Tribunal Review'],
        ];
    }

    /**
     * @return array<string, array{0: ?string, 1: ?string}>
     */
    public static function nonArtMatterProvider(): array
    {
        return [
            'partner title' => ['pv', 'Partner Visa'],
            'partner nick and title' => ['pa', 'Partner (820/801)'],
            'partnership' => [null, 'Partnership agreement'],
            'smart' => [null, 'Smart form checklist'],
            'empty' => [null, null],
            'unrelated' => ['sse', 'Student Visa 500'],
        ];
    }
}
