<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Safe HTML for displaying client note descriptions in the Notes tab.
 * Prevents pasted markup (e.g. stray </div>) from breaking the page layout
 * while preserving common rich text from editors.
 */
class NoteDescriptionHtml
{
    /**
     * Raw description bytes above this are previewed on the activity list.
     * Appointment and stage HTML always go out in full so feed chrome still works.
     */
    public const FEED_LIST_MAX_BYTES = 10240;

    private const FEED_LIST_PREVIEW_CHARS = 400;

    private static ?\HTMLPurifier $purifier = null;

    public static function isOfficeXmlPaste(string $description): bool
    {
        return strpos($description, '<xml>') !== false
            || strpos($description, '<o:OfficeDocumentSettings>') !== false;
    }

    public static function forDisplay(?string $description): string
    {
        if ($description === null || trim($description) === '') {
            return '';
        }

        if (self::isOfficeXmlPaste($description)) {
            return '<p>'.htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</p>';
        }

        return self::purifier()->purify($description);
    }

    /**
     * Activity feed list payload: full purified HTML for normal rows, escaped text preview for oversized ones.
     *
     * @return array{message: string, message_truncated: bool}
     */
    public static function forFeedList(?string $description, ?string $activityType = null): array
    {
        if ($description === null || trim($description) === '') {
            return [
                'message' => '',
                'message_truncated' => false,
            ];
        }

        if (self::mustSendFullFeedHtml($description, $activityType) || strlen($description) <= self::FEED_LIST_MAX_BYTES) {
            return [
                'message' => self::forDisplay($description),
                'message_truncated' => false,
            ];
        }

        return [
            'message' => self::feedListPreview($description),
            'message_truncated' => true,
        ];
    }

    private static function mustSendFullFeedHtml(string $description, ?string $activityType): bool
    {
        if ($activityType === 'stage') {
            return true;
        }

        return str_contains($description, 'appointment-activity-detail');
    }

    private static function feedListPreview(string $description): string
    {
        $text = html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? '';

        return htmlspecialchars(
            Str::limit($text, self::FEED_LIST_PREVIEW_CHARS, '…'),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }

    private static function purifier(): \HTMLPurifier
    {
        if (self::$purifier === null) {
            $config = \HTMLPurifier_Config::createDefault();
            // Match typical TinyMCE/Summernote output; balanced tree fixes malformed paste
            $config->set('HTML.Allowed', implode(',', [
                'p[style|class]', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'del', 'ins',
                'ul', 'ol', 'li', 'a[href|title|target|rel|name]',
                'span[style|class]', 'sub', 'sup', 'blockquote[cite]',
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'table', 'thead', 'tbody', 'tfoot', 'tr', 'td', 'th', 'colgroup', 'col',
                'img[src|alt|width|height|title|class]',
                'div[class|style]', 'hr', 'pre', 'code',
            ]));
            $config->set('URI.AllowedSchemes', [
                'http' => true,
                'https' => true,
                'mailto' => true,
                'tel' => true,
            ]);
            $config->set('Attr.AllowedFrameTargets', ['_blank', '_self']);
            $config->set('HTML.TargetBlank', true);
            $config->set('AutoFormat.RemoveEmpty', false);
            $config->set('AutoFormat.AutoParagraph', false);
            self::$purifier = new \HTMLPurifier($config);
        }

        return self::$purifier;
    }
}
