<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Concerns\EnsuresCrmRecordAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffFileTime\DoneStaffFileTimeRequest;
use App\Http\Requests\StaffFileTime\StartStaffFileTimeRequest;
use App\Http\Requests\StaffFileTime\UpdateStaffFileTimeRequest;
use App\Models\ClientMatter;
use App\Models\Staff;
use App\Models\StaffFileTimeEntry;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffDayHoursService;
use App\Services\StaffFileTimeService;
use App\Support\StaffClientVisibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardMyDayController extends Controller
{
    use EnsuresCrmRecordAccess;

    public function __construct(
        protected StaffFileTimeService $fileTime,
        protected StaffDayCrmEventsService $crmEvents,
        protected StaffDayHoursService $hours,
    ) {
        $this->middleware('auth:admin');
    }

    public function index(): JsonResponse
    {
        $staff = $this->staffOrAbort();

        $board = $this->fileTime->boardForStaff((int) $staff->id);

        return response()->json([
            'success' => true,
            'hours' => $this->hours->forStaff((int) $staff->id),
            'crm_events' => $this->crmEvents->forStaff((int) $staff->id),
            'board' => $board,
        ]);
    }

    public function start(StartStaffFileTimeRequest $request): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validated();

        if (! empty($data['client_matter_id'])) {
            $this->ensureMatterAccess((int) $data['client_matter_id']);
        }

        $entry = $this->fileTime->start((int) $staff->id, $data);

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($entry->load('clientMatter')),
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function pause(Request $request, StaffFileTimeEntry $entry): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $clock = $this->optionalClockSeconds($request);
        $updated = $this->fileTime->pause((int) $staff->id, $entry, $clock);

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($updated),
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function park(Request $request, StaffFileTimeEntry $entry): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $clock = $this->optionalClockSeconds($request);
        $updated = $this->fileTime->park((int) $staff->id, $entry, $clock);

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($updated),
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function resume(StaffFileTimeEntry $entry): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $updated = $this->fileTime->resume((int) $staff->id, $entry);

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($updated),
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function done(DoneStaffFileTimeRequest $request, StaffFileTimeEntry $entry): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validated();
        $updated = $this->fileTime->done(
            (int) $staff->id,
            $entry,
            (int) $data['confirmed_minutes'],
            isset($data['clock_seconds']) ? (int) $data['clock_seconds'] : null,
        );

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($updated),
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function reopen(StaffFileTimeEntry $entry): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $updated = $this->fileTime->reopen((int) $staff->id, $entry);

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($updated),
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function update(UpdateStaffFileTimeRequest $request, StaffFileTimeEntry $entry): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validated();

        if (! empty($data['client_matter_id'])) {
            $this->ensureMatterAccess((int) $data['client_matter_id']);
        }

        $updated = $this->fileTime->updateOpen((int) $staff->id, $entry, $data);

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($updated),
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function destroy(StaffFileTimeEntry $entry): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $this->fileTime->deleteOpen((int) $staff->id, $entry);

        return response()->json([
            'success' => true,
            'board' => $this->fileTime->boardForStaff((int) $staff->id),
        ]);
    }

    public function matterSearch(Request $request): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $like = '%'.mb_strtolower($q).'%';

        $query = ClientMatter::query()
            ->with([
                'client:id,first_name,last_name,type',
                'matter:id,title,nick_name',
                'workflowStage:id,name',
            ])
            ->where(function ($builder) use ($like) {
                $builder->whereRaw('LOWER(client_unique_matter_no) LIKE ?', [$like])
                    ->orWhereHas('client', function ($client) use ($like) {
                        $client->whereRaw('LOWER(first_name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', [$like])
                            ->orWhereRaw("LOWER(CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,''))) LIKE ?", [$like]);
                    })
                    ->orWhereHas('matter', function ($matter) use ($like) {
                        $matter->whereRaw('LOWER(title) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(nick_name) LIKE ?', [$like]);
                    });
            })
            ->orderByDesc('updated_at')
            ->limit(24);

        $rows = $query->get()->filter(function (ClientMatter $matter) use ($staff) {
            $clientId = (int) $matter->client_id;

            return $clientId > 0 && StaffClientVisibility::canAccessClientOrLead($clientId, $staff);
        })->take(8)->values();

        $results = $rows->map(function (ClientMatter $matter): array {
            $client = $matter->client;
            $clientName = $client
                ? trim(($client->first_name ?? '').' '.($client->last_name ?? ''))
                : '';
            $matterTitle = $matter->matter?->nick_name ?: ($matter->matter?->title ?: '');
            $stage = $matter->workflowStage?->name;

            return [
                'id' => $matter->id,
                'client_id' => $matter->client_id,
                'ref' => $matter->client_unique_matter_no,
                'client' => $clientName,
                'matter' => $matterTitle,
                'stage' => $stage,
            ];
        })->all();

        return response()->json([
            'success' => true,
            'results' => $results,
            'empty_hint' => $results === []
                ? 'No accessible matters found. Create the matter on the client file first.'
                : null,
        ]);
    }

    public function copySummary(): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $summary = $this->fileTime->copySummary(
            (int) $staff->id,
            $this->crmEvents,
            $this->hours,
        );

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ]);
    }

    protected function staffOrAbort(): Staff
    {
        $staff = Auth::guard('admin')->user();
        if (! $staff instanceof Staff) {
            abort(403, 'Unauthorized');
        }

        return $staff;
    }

    protected function ensureMatterAccess(int $matterId): void
    {
        $matter = ClientMatter::query()->find($matterId);
        if (! $matter) {
            abort(404, 'Matter not found');
        }
        $this->ensureCrmRecordAccess((int) $matter->client_id);
    }

    protected function optionalClockSeconds(Request $request): ?int
    {
        if (! $request->exists('clock_seconds')) {
            return null;
        }

        return $request->integer('clock_seconds');
    }
}
