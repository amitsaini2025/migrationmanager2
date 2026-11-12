<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * When a notification link carries ?t=, mark that row read only if it belongs
 * to the staff member who is logged in. Super admins viewing someone else's
 * row do not change that row.
 */
class MarkOwnedNotificationRead
{
    public function handle(Request $request, Closure $next): Response
    {
        $notificationId = (int) $request->query('t', 0);
        $staff = Auth::guard('admin')->user();
        if ($notificationId > 0 && $staff) {
            Notification::markReadIfOwned($notificationId, (int) $staff->id);
        }

        return $next($request);
    }
}
