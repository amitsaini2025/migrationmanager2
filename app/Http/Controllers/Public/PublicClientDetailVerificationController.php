<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\ClientPersonalDetailsController;
use App\Http\Requests\Public\SubmitClientDetailVerificationRequest;
use App\Models\Matter;
use App\Services\ClientDetailVerificationService;
use App\Support\ClientDetailVerificationFields;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PublicClientDetailVerificationController extends Controller
{
    public function show(string $token, ClientDetailVerificationService $service): View
    {
        $verification = $service->findUsableByToken($token);
        if (! $verification) {
            return view('public.client_detail_verification_expired');
        }

        $snapshot = is_array($verification->snapshot) ? $verification->snapshot : [];
        $client = $verification->client;

        return view('public.client_detail_verification', [
            'token' => $token,
            'submitted' => false,
            'firstName' => $client?->first_name ?: 'there',
            'values' => $snapshot,
            'submitUrl' => route('public.client-detail-verification.submit', ['token' => $token]),
            'addressSearchUrl' => route('public.client-detail-verification.search-address', ['token' => $token]),
            'addressDetailsUrl' => route('public.client-detail-verification.place-details', ['token' => $token]),
            'visaTypesUrl' => route('public.client-detail-verification.visa-types', ['token' => $token]),
            'fieldResults' => [],
        ]);
    }

    public function searchAddress(
        Request $request,
        string $token,
        ClientDetailVerificationService $service,
        ClientPersonalDetailsController $addresses,
    ): JsonResponse {
        $this->requireUsableToken($token, $service);
        $request->validate([
            'query' => ['required', 'string', 'min:3', 'max:200'],
        ]);

        return $addresses->searchAddressFull($request);
    }

    public function placeDetails(
        Request $request,
        string $token,
        ClientDetailVerificationService $service,
        ClientPersonalDetailsController $addresses,
    ): JsonResponse {
        $this->requireUsableToken($token, $service);
        $request->validate([
            'place_id' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        return $addresses->getPlaceDetails($request);
    }

    public function visaTypes(string $token, ClientDetailVerificationService $service): JsonResponse
    {
        $this->requireUsableToken($token, $service);

        if (! Schema::hasTable('matters')) {
            return response()->json([]);
        }

        $visaTypes = Matter::query()
            ->select(['id', 'title', 'nick_name'])
            ->where('title', 'not like', '%skill assessment%')
            ->where('status', 1)
            ->orderBy('title')
            ->get()
            ->map(static fn (Matter $matter): array => [
                'id' => $matter->id,
                'title' => $matter->title,
                'nick_name' => $matter->nick_name,
                'label' => ClientDetailVerificationFields::visaTypeLabel($matter->title, $matter->nick_name),
            ])
            ->values();

        return response()->json($visaTypes);
    }

    public function submit(
        SubmitClientDetailVerificationRequest $request,
        string $token,
        ClientDetailVerificationService $service,
    ): View {
        $verification = $service->findUsableByToken($token);
        if (! $verification) {
            return view('public.client_detail_verification_expired');
        }

        $fields = $request->validated('fields');

        $service->submit(
            $verification,
            $fields,
            $request->ip(),
            $request->userAgent(),
        );

        $verification->refresh()->loadMissing('sender');

        $confirmed = collect($fields)
            ->where('status', ClientDetailVerificationFields::STATUS_CONFIRMED)
            ->count();
        $changed = collect($fields)
            ->where('status', ClientDetailVerificationFields::STATUS_CHANGE_REQUESTED)
            ->count();

        $fieldResults = [];
        foreach ($fields as $field) {
            $fieldResults[(string) $field['key']] = $field;
        }

        return view('public.client_detail_verification', [
            'token' => $token,
            'submitted' => true,
            'changedCount' => $changed,
            'confirmedCount' => $confirmed,
            'resultHeading' => ClientDetailVerificationFields::resultHeading($confirmed, $changed),
            'verifiedByName' => trim((string) ($verification->sender?->full_name ?: '')) ?: '—',
            'verifiedAt' => ClientDetailVerificationFields::formatVerifiedAt($verification->submitted_at),
            'fieldResults' => $fieldResults,
            'firstName' => $verification->client?->first_name ?: 'there',
            'values' => is_array($verification->snapshot) ? $verification->snapshot : [],
            'submitUrl' => route('public.client-detail-verification.submit', ['token' => $token]),
            'addressSearchUrl' => route('public.client-detail-verification.search-address', ['token' => $token]),
            'addressDetailsUrl' => route('public.client-detail-verification.place-details', ['token' => $token]),
            'visaTypesUrl' => route('public.client-detail-verification.visa-types', ['token' => $token]),
        ]);
    }

    private function requireUsableToken(string $token, ClientDetailVerificationService $service): void
    {
        if (! $service->findUsableByToken($token)) {
            abort(Response::HTTP_NOT_FOUND, 'This link is no longer valid.');
        }
    }
}
