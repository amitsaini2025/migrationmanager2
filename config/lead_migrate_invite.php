<?php

/**
 * Staff-sent migrate-lead invite links (email / AU SMS).
 */
return [
    'path_prefix' => env('LEAD_MIGRATE_INVITE_PATH', 'migrate-lead'),
    'token_ttl_hours' => (int) env('LEAD_MIGRATE_INVITE_TTL_HOURS', 168),

    /**
     * Branch/office id for the Waiting (In Person) check-in created after form submit.
     * Falls back to the first branch row when empty.
     */
    'default_office_id' => env('LEAD_MIGRATE_INVITE_DEFAULT_OFFICE_ID'),

    /**
     * Waiting-list email goes to staff.email for constants.reception_user_id
     * (RECEPTION_USER_ID) — same reception user as existing CRM features.
     */
    'visit_purpose' => env('LEAD_MIGRATE_INVITE_VISIT_PURPOSE', 'Migration Client Enquiry'),
];
