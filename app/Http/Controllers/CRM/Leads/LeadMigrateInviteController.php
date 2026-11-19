<?php

namespace App\Http\Controllers\CRM\Leads;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\SendLeadMigrateInviteRequest;
use App\Services\LeadMigrateInviteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LeadMigrateInviteController extends Controller
{
    public function send(SendLeadMigrateInviteRequest $request, LeadMigrateInviteService $service): JsonResponse
    {
        $validated = $request->validated();

        $result = $service->sendInvite(
            (string) $validated['channel'],
            $validated['email'] ?? null,
            $validated['phone'] ?? null,
            Auth::guard('admin')->id(),
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
