<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Resolve the matter's assigned migration agent for billing PDFs (invoice / receipts).
 */
final class MatterMigrationAgent
{
    /**
     * @return array{agent_name: string|null, agent_marn: string|null}
     */
    public static function forClientMatter(?int $clientMatterId): array
    {
        if ($clientMatterId === null || $clientMatterId <= 0) {
            return [
                'agent_name' => null,
                'agent_marn' => null,
            ];
        }

        $row = DB::table('client_matters')
            ->leftJoin('staff', 'staff.id', '=', 'client_matters.sel_migration_agent')
            ->where('client_matters.id', $clientMatterId)
            ->select(
                'staff.first_name',
                'staff.last_name',
                'staff.marn_number'
            )
            ->first();

        if (! $row) {
            return [
                'agent_name' => null,
                'agent_marn' => null,
            ];
        }

        $name = trim(implode(' ', array_filter([
            $row->first_name ?? null,
            $row->last_name ?? null,
        ])));

        $marn = trim((string) ($row->marn_number ?? ''));

        return [
            'agent_name' => $name !== '' ? $name : null,
            'agent_marn' => $marn !== '' ? $marn : null,
        ];
    }
}
