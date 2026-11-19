<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\LeadMigrateInviteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicLeadMigrateInviteController extends Controller
{
    public function show(string $token, LeadMigrateInviteService $service): View
    {
        $invite = $service->findUsableByToken($token);

        if (! $invite) {
            return view('public.lead_migrate_invite_expired');
        }

        return view('public.lead_migrate_invite_form', [
            'invite' => $invite,
            'token' => $token,
            'formAction' => route('public.lead-migrate-invite.submit', ['token' => $token]),
            'prefillEmail' => $invite->isEmailChannel() ? (string) $invite->sent_to_email : '',
            'prefillPhone' => $invite->isSmsChannel() ? (string) $invite->sent_to_phone : '',
        ]);
    }

    public function submit(string $token, Request $request, LeadMigrateInviteService $service): RedirectResponse|View
    {
        $invite = $service->findUsableByToken($token);

        if (! $invite) {
            return view('public.lead_migrate_invite_expired');
        }

        $validated = $request->validate(LeadMigrateInviteService::publicFormRules());

        try {
            $service->submit(
                $invite,
                $validated,
                $request->ip(),
                $request->userAgent(),
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Migrate lead invite submit failed', [
                'invite_id' => $invite->id,
                'exception' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['form' => __('We could not save your enquiry. Please try again later or call our office.')]);
        }

        return view('public.lead_migrate_invite_thanks');
    }
}
