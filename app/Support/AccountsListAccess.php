<?php

namespace App\Support;

use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Accountant (accounts team) use of the four money lists.
 * List actions do not open the client file. Super-admin-only files stay hidden.
 */
class AccountsListAccess
{
    public static function accountantRoleId(): int
    {
        return (int) config('crm_access.accountant_role_id', 15);
    }

    public static function isAccountant(?Authenticatable $user): bool
    {
        if (! $user) {
            return false;
        }

        return (int) ($user->role ?? 0) === self::accountantRoleId();
    }

    /**
     * @return array<string, string>
     */
    public static function reasonLabels(): array
    {
        $labels = config('crm_access.quick_reason_options', []);
        $code = (string) config('crm_access.accounts_open_reason.code', 'accounts_validation');
        $label = (string) config('crm_access.accounts_open_reason.label', 'Accounts validation');
        if ($code !== '') {
            $labels[$code] = $label;
        }

        return $labels;
    }

    /**
     * Accountant may read and validate this client from the accounts lists.
     * Locked super-admin files stay closed.
     */
    public static function accountantMayUseClient(int $adminId, ?Authenticatable $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (! self::isAccountant($user) || $adminId <= 0) {
            return false;
        }

        $row = Admin::query()
            ->where('id', $adminId)
            ->whereIn('type', ['client', 'lead'])
            ->first(['id', 'type', 'client_id']);

        if (! $row) {
            return false;
        }

        return ! StaffClientVisibility::isSuperAdminOnlyLockedClient($row->type, $row->client_id);
    }

    /**
     * Hide super-admin-only client files from a receipt/invoice query.
     *
     * @param  Builder|\Illuminate\Database\Query\Builder  $query
     */
    public static function excludeLockedClients($query, string $clientIdColumn = 'client_id'): void
    {
        $viewer = Auth::user();
        if (! $viewer || (int) ($viewer->role ?? 0) === 1) {
            return;
        }

        $upperIds = StaffClientVisibility::normalizedSuperAdminOnlyLockedClientFileIdsUpper();
        if ($upperIds === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($upperIds), '?'));
        $query->whereNotIn($clientIdColumn, function ($sub) use ($upperIds, $placeholders) {
            $sub->select('id')
                ->from('admins')
                ->where('type', 'client')
                ->whereRaw("UPPER(TRIM(client_id)) IN ({$placeholders})", $upperIds);
        });
    }
}
