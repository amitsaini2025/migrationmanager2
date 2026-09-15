<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffDayHoursService;
use App\Services\StaffFileTimeService;
use App\Services\StaffMatterSessionService;
use App\Services\StaffWorkloadService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffWorkloadController extends Controller
{
    public function __construct(
        protected StaffWorkloadService $staffWorkloadService,
        protected StaffFileTimeService $staffFileTimeService,
        protected StaffDayCrmEventsService $staffDayCrmEventsService,
        protected StaffDayHoursService $staffDayHoursService,
        protected StaffMatterSessionService $staffMatterSessionService,
    ) {
        $this->middleware('auth:admin');
        $this->middleware('adminconsole');
    }

    public function index(Request $request): View
    {
        $day = $this->parseDay($request->query('date'));
        [$start] = $this->staffWorkloadService->dayBounds($day);
        $rows = $this->staffWorkloadService->getAdminWorkloadRows($start);

        return view('AdminConsole.staff.workload', [
            'rows' => $rows,
            'selectedDate' => $start->toDateString(),
            'dateLabel' => $start->format('l, j M Y'),
        ]);
    }

    public function myDay(Request $request, Staff $staff): JsonResponse
    {
        $day = $this->parseDay($request->query('date'));

        $summary = $this->staffFileTimeService->copySummary(
            (int) $staff->id,
            $this->staffDayCrmEventsService,
            $this->staffDayHoursService,
            $this->staffMatterSessionService,
            $day,
        );

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => (int) $staff->id,
                'name' => trim(($staff->first_name ?? '').' '.($staff->last_name ?? '')),
            ],
            'summary' => $summary,
        ]);
    }

    protected function parseDay(mixed $dateInput): ?Carbon
    {
        if (! is_string($dateInput) || $dateInput === '') {
            return null;
        }

        try {
            return Carbon::parse($dateInput, (string) config('app.timezone'))->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
