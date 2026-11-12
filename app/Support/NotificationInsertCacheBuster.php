<?php

namespace App\Support;

/**
 * Clears the CRM bell cache for receivers named in a raw notifications INSERT.
 * Eloquent writes are handled by NotificationObserver. Query-builder inserts are not.
 */
class NotificationInsertCacheBuster
{
    /**
     * @param  array<int, mixed>  $bindings
     */
    public static function forgetReceivers(string $sql, array $bindings): void
    {
        if (! preg_match('/insert\s+into\s+["`]?notifications["`]?\s*\(([^)]+)\)/i', $sql, $matches)) {
            return;
        }

        $columns = array_map(
            static fn (string $column): string => strtolower(trim($column, " \t\n\r\0\x0B\"`")),
            explode(',', $matches[1])
        );
        $index = array_search('receiver_id', $columns, true);
        $width = count($columns);
        if ($index === false || $width === 0) {
            return;
        }

        $seen = [];
        for ($offset = $index; $offset < count($bindings); $offset += $width) {
            $receiverId = (int) $bindings[$offset];
            if ($receiverId < 1 || isset($seen[$receiverId])) {
                continue;
            }
            $seen[$receiverId] = true;
            CachedHeaderCounts::forgetNotificationUnread($receiverId);
        }
    }
}
