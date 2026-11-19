<?php

/**
 * Staff-sent migrate-lead invite links (email / AU SMS).
 */
return [
    'path_prefix' => env('LEAD_MIGRATE_INVITE_PATH', 'migrate-lead'),
    'token_ttl_hours' => (int) env('LEAD_MIGRATE_INVITE_TTL_HOURS', 168),
];
