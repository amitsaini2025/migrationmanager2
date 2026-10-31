<?php

namespace Tests\Unit;

use App\Support\VisaSheetCategory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VisaSheetCategoryTest extends TestCase
{
    /**
     * @return array<string, list<string>>
     */
    private static function trRules(): array
    {
        $config = require dirname(__DIR__, 2).'/config/sheets/visa_types.php';

        return $config['tr']['refused_visa_type_suggest'];
    }

    #[Test]
    public function tr_sheet_derives_stream_from_the_matter(): void
    {
        $config = require dirname(__DIR__, 2).'/config/sheets/visa_types.php';

        $this->assertTrue(VisaSheetCategory::derivesFromMatter($config['tr']));
        $this->assertFalse(VisaSheetCategory::derivesFromMatter($config['visitor']));
        $this->assertArrayHasKey('subsequent', $config['tr']['refused_visa_type_options']);
    }

    #[Test]
    #[DataProvider('matterProvider')]
    public function resolves_485_matter_to_stream(?string $title, ?string $nick, ?string $expected): void
    {
        $this->assertSame($expected, VisaSheetCategory::resolve($title, $nick, self::trRules()));
    }

    #[Test]
    public function sql_checks_second_post_higher_before_post_higher(): void
    {
        $sql = VisaSheetCategory::sqlCase('latest_matter.matter_title', 'latest_matter.matter_nick', self::trRules());

        $second = strpos($sql, "THEN 'second-post-higher'");
        $postHigher = strpos($sql, "THEN 'post-higher'");
        $vocational = strpos($sql, "THEN 'post-vocational'");
        $subsequent = strpos($sql, "THEN 'subsequent'");

        $this->assertNotFalse($second);
        $this->assertNotFalse($subsequent);
        $this->assertNotFalse($vocational);
        $this->assertNotFalse($postHigher);
        $this->assertLessThan($subsequent, $second);
        $this->assertLessThan($vocational, $subsequent);
        $this->assertLessThan($postHigher, $vocational);
        $this->assertStringContainsString('post-study', $sql);
        $this->assertStringContainsString('post study', $sql);
        $this->assertStringNotContainsString('graduate', $sql);
        $this->assertStringContainsString('STRPOS(', $sql);
        $this->assertStringNotContainsString('ESCAPE', $sql);
    }

    #[Test]
    public function rejects_unsafe_sql_expressions(): void
    {
        $sql = VisaSheetCategory::sqlCase('m.title; drop table matters', 'm.nick_name', [
            'post-higher' => ['post study'],
        ]);

        $this->assertStringContainsString('COALESCE(NULL', $sql);
        $this->assertStringNotContainsString('drop table', $sql);
    }

    /**
     * @return array<string, array{0: ?string, 1: ?string, 2: ?string}>
     */
    public static function matterProvider(): array
    {
        return [
            'vocational title' => ['485 - Temporary Graduate - Vocational', '485V', 'post-vocational'],
            'post-study title' => ['485 - Temporary Graduate - Post-Study Work', '485PS', 'post-higher'],
            'post study spaced' => ['485 Post Study Work', null, 'post-higher'],
            'second post higher' => ['485 Temporary Graduate_Second Post Higher Education Work', '485SPH', 'second-post-higher'],
            'subsequent entrant' => ['485 -Temporary Graduate - Subsequent Entrant', 'Subsequent485', 'subsequent'],
            'graduate alone is not a stream' => ['485 - Temporary Graduate', null, null],
            'empty' => [null, null, null],
        ];
    }
}
