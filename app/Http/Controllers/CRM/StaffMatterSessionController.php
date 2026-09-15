<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Concerns\EnsuresCrmRecordAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffMatterSession\HeartbeatStaffMatterSessionRequest;
use App\Http\Requests\StaffMatterSession\IdleCutStaffMatterSessionRequest;
use App\Http\Requests\StaffMatterSession\UpdateStaffMatterSessionRequest;
use App\Models\Staff;
use App\Models\StaffMatterSession;
use App\Services\StaffMatterSessionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StaffMatterSessionController extends Controller
{
    use EnsuresCrmRecordAccess;

    public function __construct(
        protected StaffMatterSessionService $sessions,
    ) {
        $this->middleware('auth:admin');
    }

    public function heartbeat(HeartbeatStaffMatterSessionRequest $request): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validated();
        $clientId = (int) $data['client_id'];
        $this->ensureCrmRecordAccess($clientId);

        $matterId = $this->optionalMatterId($data);
        $session = $this->sessions->heartbeat(
            (int) $staff->id,
            $clientId,
            $matterId,
            (int) $data['focused_seconds'],
        );

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($session),
        ]);
    }

    public function blur(HeartbeatStaffMatterSessionRequest $request): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validated();
        $clientId = (int) $data['client_id'];
        $this->ensureCrmRecordAccess($clientId);

        $matterId = $this->optionalMatterId($data);
        $session = $this->sessions->blur(
            (int) $staff->id,
            $clientId,
            $matterId,
            (int) $data['focused_seconds'],
        );

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($session),
        ]);
    }

    public function idleCut(IdleCutStaffMatterSessionRequest $request, StaffMatterSession $session): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $idleStarted = Carbon::parse($request->validated('idle_started_at'));
        $updated = $this->sessions->idleCut((int) $staff->id, $session, $idleStarted);

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($updated),
        ]);
    }

    public function update(UpdateStaffMatterSessionRequest $request, StaffMatterSession $session): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $updated = $this->sessions->updateMinutes(
            (int) $staff->id,
            $session,
            (int) $request->validated('confirmed_minutes'),
        );

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($updated),
        ]);
    }

    public function destroy(StaffMatterSession $session): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $this->sessions->delete((int) $staff->id, $session);

        return response()->json(['success' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeSession(StaffMatterSession $session): array
    {
        return [
            'id' => $session->id,
            'status' => $session->status,
            'focused_seconds' => (int) $session->focused_seconds,
            'confirmed_minutes' => $session->confirmed_minutes,
            'is_reviewed_only' => (bool) $session->is_reviewed_only,
            'activities_log_id' => $session->activities_log_id,
        ];
    }

    protected function staffOrAbort(): Staff
    {
        $staff = Auth::guard('admin')->user();
        if (! $staff instanceof Staff) {
            abort(403, 'Unauthorized');
        }

        return $staff;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function optionalMatterId(array $data): ?int
    {
        if (! array_key_exists('client_matter_id', $data) || $data['client_matter_id'] === null || $data['client_matter_id'] === '') {
            return null;
        }

        $matterId = (int) $data['client_matter_id'];

        return $matterId > 0 ? $matterId : null;
    }
}
