<?php

namespace App\Support;

use App\Models\ClientMatter;
use Illuminate\Support\Facades\Schema;

/**
 * Visa sheet Checklist-tab status column helpers (active / hold / convert_to_client / discontinue).
 */
class VisaSheetChecklistStatus
{
    /**
     * Whether returning to Checklist should clear a Convert to client sheet status.
     */
    public static function shouldRestoreActiveAfterReturnToChecklist(string $newStageName, ?string $currentStatus): bool
    {
        if (strtolower(trim($newStageName)) !== 'checklist') {
            return false;
        }

        return strtolower(trim((string) $currentStatus)) === 'convert_to_client';
    }

    /**
     * If the matter lands on Checklist after Convert to client, restore sheet status to active
     * so the row reappears on the Checklist tab. Leaves hold / discontinue / active unchanged.
     *
     * Sets the attribute on the model (caller should save). Returns true when a change was applied.
     */
    public static function restoreActiveIfReturnedToChecklist(ClientMatter $matter, string $newStageName): bool
    {
        if (strtolower(trim($newStageName)) !== 'checklist') {
            return false;
        }

        $matter->loadMissing('matter');
        $sheetType = $matter->getVisaSheetType();
        if ($sheetType === null || $sheetType === '') {
            return false;
        }

        $column = (string) (config("sheets.visa_types.{$sheetType}.checklist_status_column") ?? '');
        if ($column === '' || ! Schema::hasColumn('client_matters', $column)) {
            return false;
        }

        $current = $matter->getAttribute($column);
        if ($current === null && $matter->exists) {
            $current = $matter->newQuery()->whereKey($matter->getKey())->value($column);
        }

        if (! self::shouldRestoreActiveAfterReturnToChecklist($newStageName, $current !== null ? (string) $current : null)) {
            return false;
        }

        $matter->setAttribute($column, 'active');

        return true;
    }
}
