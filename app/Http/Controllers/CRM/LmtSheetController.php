<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ClientMatter;
use App\Support\CrmSheets;
use App\Support\LmtAdvertisementFiles;
use App\Support\LmtMatterWriter;
use App\Support\LmtStatus;
use App\Support\StaffClientVisibility;
use App\Traits\ClientAuthorization;
use App\Traits\LogsClientActivity;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class LmtSheetController extends Controller
{
    use ClientAuthorization;
    use LogsClientActivity;

    public function __construct(
        private LmtMatterWriter $writer,
        private LmtAdvertisementFiles $advertisements,
    ) {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $this->assertSheetAccess();

        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');
        if (! array_key_exists($status, LmtStatus::options())) {
            $status = '';
        }

        $perPage = (int) $request->input('per_page', 25);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $rows = $this->mapRows($this->matterQuery($search)->get());
        if ($status !== '') {
            $rows = $rows->where('status_key', $status)->values();
        }

        return view('crm.clients.sheets.lmt', [
            'rows' => $this->paginate($rows, $perPage, $request),
            'search' => $search,
            'status' => $status,
            'perPage' => $perPage,
            'statusOptions' => LmtStatus::options(),
        ]);
    }

    public function companies(Request $request): JsonResponse
    {
        $this->assertSheetAccess();

        $term = trim((string) $request->input('q', ''));
        if (mb_strlen($term) < 1) {
            return response()->json(['companies' => []]);
        }

        $like = '%'.$term.'%';
        $companies = $this->companyQuery()
            ->where(function ($query) use ($like) {
                $query->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('client_id', 'like', $like)
                    ->orWhereHas('company', function ($company) use ($like) {
                        $company->where('company_name', 'like', $like);
                    });
            })
            ->with('company:id,admin_id,company_name')
            ->orderBy('first_name')
            ->limit(20)
            ->get();

        return response()->json([
            'companies' => $companies->map(fn (Admin $company) => [
                'id' => $company->id,
                'name' => $this->companyName($company),
            ])->values(),
        ]);
    }

    public function matters(Admin $company): JsonResponse
    {
        $this->assertSheetAccess();

        if (! $this->canUseCompany($company)) {
            abort(404);
        }

        $matters = ClientMatter::query()
            ->active()
            ->where('client_id', $company->id)
            ->with('matter:id,title,nick_name')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (ClientMatter $matter) => $this->canUseMatter($matter))
            ->values();

        return response()->json([
            'matters' => $matters->map(fn (ClientMatter $matter) => [
                'id' => $matter->id,
                'label' => $this->matterLabel($matter),
            ])->values(),
        ]);
    }

    public function show(ClientMatter $matter): JsonResponse
    {
        $this->assertSheetAccess();

        $company = $matter->client;
        if (! $company || ! $this->canUseCompany($company) || ! $this->canUseMatter($matter)) {
            abort(404);
        }

        $matter->loadMissing('matter:id,title,nick_name');
        $required = $matter->lmt_required;
        $requiredValue = '';
        if ($required === true) {
            $requiredValue = '1';
        } elseif ($required === false) {
            $requiredValue = '0';
        }

        $files = $this->advertisements->filesForMatters([(int) $matter->id])[(int) $matter->id] ?? [];

        return response()->json([
            'company_id' => $company->id,
            'company_name' => $this->companyName($company),
            'matter_id' => $matter->id,
            'matter_label' => $this->matterLabel($matter),
            'lmt_required' => $requiredValue,
            'lmt_start_date' => $matter->lmt_start_date?->format('Y-m-d') ?? '',
            'lmt_end_date' => $matter->lmt_end_date?->format('Y-m-d') ?? '',
            'lmt_notes' => $matter->lmt_notes ?? '',
            'lmt_password' => $matter->lmt_password ?? '',
            'files' => $files,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->assertSheetAccess();

        $company = Admin::query()->find((int) $request->input('company_id'));
        if (! $company || ! $this->canUseCompany($company)) {
            return response()->json(['success' => false, 'message' => 'Company not found.'], 404);
        }

        $matter = ClientMatter::query()
            ->where('id', (int) $request->input('client_matter_id'))
            ->where('client_id', $company->id)
            ->first();
        if (! $matter || ! $this->canUseMatter($matter)) {
            return response()->json(['success' => false, 'message' => 'Matter not found.'], 404);
        }

        $files = $request->file('advertisements', []);
        if (! is_array($files)) {
            $files = [$files];
        }
        $fileErrors = $this->advertisements->validateFiles($files);
        if ($fileErrors !== []) {
            return response()->json(['success' => false, 'message' => $fileErrors[0]], 422);
        }

        $result = $this->writer->save($company, [
            'client_matter_id' => $request->input('client_matter_id'),
            'lmt_required' => $request->input('lmt_required'),
            'lmt_start_date' => $request->input('lmt_start_date'),
            'lmt_end_date' => $request->input('lmt_end_date'),
            'lmt_notes' => $request->input('lmt_notes'),
            'lmt_password' => $request->input('lmt_password'),
        ]);

        if (! $result['ok']) {
            return response()->json(['success' => false, 'message' => $result['message']], $result['status']);
        }

        if ($files !== []) {
            $this->advertisements->store($company, $matter, $files, (int) auth('admin')->id());
            $this->logClientActivity(
                (int) $company->id,
                'uploaded LMT advertisement',
                '<p>Uploaded Labour Market Testing advertisement copies for '.$this->matterLabel($matter).'</p>',
                'document'
            );
        }

        return response()->json(['success' => true, 'message' => $result['message']]);
    }

    private function assertSheetAccess(): void
    {
        if (! $this->hasModuleAccess('20') || ! $this->canAccessCrmSheet(CrmSheets::KEY_LMT)) {
            abort(403, 'Unauthorized');
        }
    }

    private function canUseMatter(ClientMatter $matter): bool
    {
        if ((int) $matter->matter_status !== 1) {
            return false;
        }

        $paId = StaffClientVisibility::personAssistingStaffIdOrNull(auth('admin')->user());
        if ($paId === null) {
            return true;
        }

        return in_array($paId, [
            (int) $matter->sel_migration_agent,
            (int) $matter->sel_person_responsible,
            (int) $matter->sel_person_assisting,
        ], true);
    }

    /**
     * @return Builder<ClientMatter>
     */
    private function matterQuery(string $search): Builder
    {
        $query = ClientMatter::query()
            ->active()
            ->where(function ($lmt) {
                $lmt->whereNotNull('lmt_required')
                    ->orWhereNotNull('lmt_start_date')
                    ->orWhereNotNull('lmt_end_date')
                    ->orWhere(function ($notes) {
                        $notes->whereNotNull('lmt_notes')->where('lmt_notes', '!=', '');
                    })
                    ->orWhere(function ($password) {
                        $password->whereNotNull('lmt_password')->where('lmt_password', '!=', '');
                    });
            })
            ->whereHas('client', function ($client) {
                $this->constrainCompany($client);
            })
            ->with(['client.company', 'matter:id,title,nick_name']);

        if ($paId = StaffClientVisibility::personAssistingStaffIdOrNull(auth('admin')->user())) {
            $query->where(function ($assigned) use ($paId) {
                $assigned->where('sel_migration_agent', $paId)
                    ->orWhere('sel_person_responsible', $paId)
                    ->orWhere('sel_person_assisting', $paId);
            });
        }

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($match) use ($like) {
                $match->where('client_unique_matter_no', 'like', $like)
                    ->orWhereHas('matter', fn ($matter) => $matter->where('title', 'like', $like))
                    ->orWhereHas('client', function ($client) use ($like) {
                        $client->where('first_name', 'like', $like)
                            ->orWhere('client_id', 'like', $like)
                            ->orWhereHas('company', fn ($company) => $company->where('company_name', 'like', $like));
                    });
            });
        }

        return $query->orderByDesc('id');
    }

    /**
     * @param  Builder<Admin>  $query
     */
    private function constrainCompany(Builder $query): void
    {
        $query->where('is_company', 1)
            ->where('is_archived', 0)
            ->whereNull('is_deleted')
            ->whereIn('type', ['client', 'lead']);

        StaffClientVisibility::restrictAdminEloquentQuery($query);
    }

    /**
     * @return Builder<Admin>
     */
    private function companyQuery(): Builder
    {
        $query = Admin::query();
        $this->constrainCompany($query);

        return $query;
    }

    private function canUseCompany(Admin $company): bool
    {
        return $this->companyQuery()->whereKey($company->id)->exists();
    }

    /**
     * @param  Collection<int, ClientMatter>  $matters
     * @return Collection<int, object>
     */
    private function mapRows(Collection $matters): Collection
    {
        $files = $this->advertisements->filesForMatters($matters->pluck('id')->all());
        $today = Carbon::today();

        return $matters->map(function (ClientMatter $matter) use ($files, $today) {
            $status = LmtStatus::assess(
                $matter->lmt_required,
                $matter->lmt_start_date,
                $matter->lmt_end_date,
                $today
            );
            $hasNotes = trim((string) ($matter->lmt_notes ?? '')) !== '';
            $hasPassword = trim((string) ($matter->lmt_password ?? '')) !== '';
            if ($status['key'] === LmtStatus::NOT_RECORDED && ($hasNotes || $hasPassword)) {
                $status['key'] = LmtStatus::INCOMPLETE;
                $status['label'] = LmtStatus::options()[LmtStatus::INCOMPLETE];
                $status['detail'] = 'Start date and end date are both required.';
            }
            $company = $matter->client;

            return (object) [
                'matter_id' => $matter->id,
                'company_id' => $company?->id,
                'company_name' => $company ? $this->companyName($company) : '—',
                'matter_label' => $this->matterLabel($matter),
                'detail_url' => $company ? $this->detailUrl($company, $matter) : null,
                'required_label' => $matter->lmt_required === null ? '—' : ($matter->lmt_required ? 'Yes' : 'No'),
                'start' => $matter->lmt_start_date?->format('d/m/Y'),
                'end' => $matter->lmt_end_date?->format('d/m/Y'),
                'span_days' => $status['span_days'],
                'password_set' => trim((string) ($matter->lmt_password ?? '')) !== '',
                'notes' => trim((string) ($matter->lmt_notes ?? '')),
                'status_key' => $status['key'],
                'status_label' => $status['label'],
                'status_detail' => $status['detail'],
                'files' => $files[(int) $matter->id] ?? [],
            ];
        })->sortBy([
            ['company_name', 'asc'],
            ['matter_label', 'asc'],
        ])->values();
    }

    private function companyName(Admin $company): string
    {
        $company->loadMissing('company');
        $name = trim((string) ($company->company?->company_name ?? ''));
        if ($name !== '') {
            return $name;
        }

        $fallback = trim((string) $company->first_name.' '.(string) $company->last_name);

        return $fallback !== '' ? $fallback : 'Company';
    }

    private function matterLabel(ClientMatter $matter): string
    {
        $matter->loadMissing('matter');
        $ref = trim((string) ($matter->client_unique_matter_no ?? ''));
        $title = trim((string) ($matter->matter?->title ?? ''));
        if ($ref !== '' && $title !== '') {
            return $ref.' — '.$title;
        }

        return $title !== '' ? $title : ($ref !== '' ? $ref : 'Matter');
    }

    private function detailUrl(Admin $company, ClientMatter $matter): string
    {
        return route('clients.detail', [
            'client_id' => base64_encode(convert_uuencode((string) $company->id)),
            'client_unique_matter_ref_no' => $matter->client_unique_matter_no ?? '',
        ]);
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function paginate(Collection $rows, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = max(1, (int) $request->input('page', 1));
        $slice = $rows->slice(($page - 1) * $perPage, $perPage)->values();

        return new Paginator($slice, $rows->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }
}
