<?php

namespace App\Traits;

use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\Auth;

trait LogsClientActivity
{
    /**
     * Log client activity to activities_logs table
     * 
     * @param int $clientId The client ID
     * @param string $subject The activity subject (e.g., "updated personal information")
     * @param string $description Optional description/details
     * @param string $activityType The activity type (default: 'activity')
     * @return ActivitiesLog
     */
    protected function logClientActivity($clientId, $subject, $description = '', $activityType = 'activity')
    {
        return ActivitiesLog::create([
            'client_id' => $clientId,
            'created_by' => Auth::user()->id ?? Auth::id(),
            'subject' => $subject,
            'description' => $description,
            'activity_type' => $activityType,
        ]);
    }

    /**
     * Log client activity with field change details
     * 
     * @param int $clientId The client ID
     * @param string $subject The activity subject
     * @param array $changedFields Array of changed field names
     * @param string $activityType The activity type (default: 'activity')
     * @return ActivitiesLog
     */
    protected function logClientActivityWithChanges($clientId, $subject, array $changedFields = [], $activityType = 'activity')
    {
        $description = '';
        if (!empty($changedFields)) {
            $description = 'Updated fields: ' . implode(', ', $changedFields);
        }

        return $this->logClientActivity($clientId, $subject, $description, $activityType);
    }
}

