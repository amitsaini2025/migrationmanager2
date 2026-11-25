<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FrontDeskCheckIn;
use App\Services\FrontDesk\CheckInLookupService;
use App\Services\FrontDesk\PublicCheckInService;
use App\Services\LeadEnquiryProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicFrontDeskCheckInController extends Controller
{
    public function __construct(
        private readonly PublicCheckInService $checkIns,
        private readonly CheckInLookupService $lookup,
    ) {}

    public function index(): View
    {
        return view('public.front_desk_checkin', [
            'visitReasons' => FrontDeskCheckIn::visitReasons(),
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => $this->lookup->australianPhoneValidationRules(),
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $matches = $this->checkIns->matches($validated['phone'], $validated['email'] ?? null);

        return response()->json([
            'matches' => $matches,
        ]);
    }

    public function appointments(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => $this->lookup->australianPhoneValidationRules(),
            'email' => ['nullable', 'email', 'max:255'],
            'admin_id' => ['required', 'integer', 'min:1'],
        ]);

        $appointments = $this->checkIns->todaysAppointments(
            $validated['phone'],
            $validated['email'] ?? null,
            (int) $validated['admin_id']
        );

        return response()->json(['appointments' => $appointments]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => $this->lookup->australianPhoneValidationRules(),
            'email' => ['nullable', 'email', 'max:255'],
            'admin_id' => ['nullable', 'integer', 'min:1'],
            'admin_type' => ['nullable', 'string', Rule::in(['client', 'lead'])],
            'appointment_id' => ['nullable', 'integer', 'min:1'],
            'claimed_appointment' => ['nullable', 'boolean'],
            'visit_reason' => ['nullable', 'string', 'max:100', Rule::in(array_keys(FrontDeskCheckIn::visitReasons()))],
            'visit_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $result = $this->checkIns->submit($validated, $request->ip());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable) {
            return $this->saveFailed();
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    public function createLead(Request $request): JsonResponse
    {
        $validated = $request->validate(array_merge([
            'first_name' => ['required', 'string', 'max:70'],
            'last_name' => ['required', 'string', 'max:70'],
            'phone' => $this->lookup->australianPhoneValidationRules(),
            'email' => ['required', 'email', 'max:150'],
            'visit_reason' => ['required', 'string', 'max:100', Rule::in(array_keys(FrontDeskCheckIn::visitReasons()))],
            'visit_notes' => ['nullable', 'string', 'max:2000'],
        ], LeadEnquiryProfileService::detailRules()));

        try {
            $result = $this->checkIns->createLead($validated, $request->ip());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable) {
            return $this->saveFailed();
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    private function saveFailed(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'We could not save your check-in. Please try again or speak with reception.',
        ], 500);
    }
}
