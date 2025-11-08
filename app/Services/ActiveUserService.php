<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\UserLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ActiveUserService
{
    /**
     * Fetch active users leveraging session and login logs.
     *
     * @param  int  $onlineThresholdMinutes
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getActiveUsers(int $onlineThresholdMinutes = 5): Collection
    {
        $cacheKey = "active_users_{$onlineThresholdMinutes}";

        return Cache::remember($cacheKey, 60, function () use ($onlineThresholdMinutes) {
            $threshold = Carbon::now()->subMinutes($onlineThresholdMinutes)->timestamp;

            $sessions = DB::table('sessions')
                ->select(['user_id', 'last_activity'])
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $threshold)
                ->get()
                ->groupBy(fn ($session) => (int) $session->user_id)
                ->map(function ($records) {
                    $latest = $records->max('last_activity');

                    return [
                        'last_activity' => Carbon::createFromTimestamp((int) $latest),
                    ];
                });

            $adminIds = $sessions->keys();

            if ($adminIds->isEmpty()) {
                return collect();
            }

            $admins = Admin::query()
                ->select(['id', 'first_name', 'last_name', 'email', 'role', 'team', 'updated_at'])
                ->whereIn('id', $adminIds)
                ->orderBy('first_name')
                ->get();

            return $admins->map(function (Admin $admin) use ($sessions) {
                $session = $sessions->get($admin->id);
                $lastLogin = $this->resolveLastLogin($admin->id, $admin->updated_at);

                return [
                    'id' => $admin->id,
                    'name' => trim("{$admin->first_name} {$admin->last_name}") ?: $admin->email,
                    'role' => $admin->role,
                    'team' => $admin->team,
                    'status' => 'online',
                    'last_activity' => $session ? $session['last_activity']->toIso8601String() : null,
                    'last_login' => $lastLogin?->toIso8601String(),
                ];
            });
        });
    }

    /**
     * Resolve the most recent login timestamp for a user.
     */
    protected function resolveLastLogin(int $userId, ?Carbon $fallback): ?Carbon
    {
        $userLog = UserLog::query()
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('message', 'like', '%Logged in%')
                    ->orWhere('message', 'like', '%Logged in successfully%');
            })
            ->latest('created_at')
            ->first();

        if ($userLog) {
            return Carbon::parse($userLog->created_at);
        }

        return $fallback ? Carbon::parse($fallback) : null;
    }
}


