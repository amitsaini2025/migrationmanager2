@props(['workload' => []])

@php
    $completed = $workload['completed_excl_call'] ?? [];
    $updated = $workload['updated'] ?? [];
    $pending = $workload['pending'] ?? [];
    $callCompleted = $workload['call_completed'] ?? [];

    $pendingTotal = (int) ($pending['total'] ?? 0);
    $completedTotal = (int) ($completed['total'] ?? 0);
    $callCompletedTotal = (int) ($callCompleted['total'] ?? 0);
    $updatedTotal = (int) ($updated['total'] ?? 0);
    $doneTotal = $completedTotal + $callCompletedTotal;

    $pendingClients = (int) ($pending['clients'] ?? 0);
    $pendingLeads = (int) ($pending['leads'] ?? 0);
    $pendingPersonal = (int) ($pending['personal'] ?? 0);
    $pendingCall = (int) ($pending['call'] ?? 0);
    $pendingOther = (int) ($pending['other'] ?? 0);
@endphp

<section class="workload-strip workload-strip--compact" aria-label="My workload today">
    <div class="workload-strip-header">
        <h2>My Workload — Today</h2>
        <span class="workload-strip-date">{{ $workload['date_label'] ?? '' }} ({{ $workload['timezone'] ?? config('app.timezone') }})</span>
    </div>

    <div class="workload-queue-bar">
        {{-- Queue first: only actionable tally --}}
        <div
            class="workload-chip workload-chip--queue"
            role="button"
            data-workload-metric="pending"
            tabindex="0"
            title="Open queue details"
            aria-label="Queue: {{ $pendingTotal }} pending actions"
        >
            <span class="workload-chip-label">Queue</span>
            <span class="workload-chip-count">{{ number_format($pendingTotal) }}</span>
            <span class="workload-chip-meta">
                {{ $pendingClients }} clients · {{ $pendingLeads }} leads
                @if($pendingPersonal > 0)· {{ $pendingPersonal }} personal @endif
            </span>
            @if($pendingCall > 0 || $pendingOther > 0)
                <span class="workload-chip-meta">Call: {{ $pendingCall }} · Other: {{ $pendingOther }}</span>
            @endif
            <a href="{{ route('assignee.action') }}" class="workload-chip-link" onclick="event.stopPropagation()">View queue →</a>
        </div>

        {{-- Done: one visual chip, two drill-downs (other vs call) --}}
        <div class="workload-chip workload-chip--done" aria-label="Done today: {{ $doneTotal }}">
            <span class="workload-chip-label">Done</span>
            <span class="workload-chip-count" aria-hidden="true">{{ number_format($doneTotal) }}</span>
            <span class="workload-chip-split">
                <button
                    type="button"
                    class="workload-chip-part"
                    data-workload-metric="completed_excl_call"
                    title="Other actions completed today"
                    aria-label="{{ $completedTotal }} other actions completed"
                >{{ number_format($completedTotal) }} other</button>
                <span class="workload-chip-sep" aria-hidden="true">·</span>
                <button
                    type="button"
                    class="workload-chip-part"
                    data-workload-metric="call_completed"
                    title="Call actions completed today"
                    aria-label="{{ $callCompletedTotal }} call actions completed"
                >{{ number_format($callCompletedTotal) }} call</button>
            </span>
        </div>

        {{-- Updates: demoted end-of-day tally --}}
        <div
            class="workload-chip workload-chip--updates"
            role="button"
            data-workload-metric="updated"
            tabindex="0"
            title="Action updates today"
            aria-label="Updates: {{ $updatedTotal }} action updates today"
        >
            <span class="workload-chip-label">Updates</span>
            <span class="workload-chip-count">{{ number_format($updatedTotal) }}</span>
            <span class="workload-chip-meta">Action updates today</span>
        </div>
    </div>

    <p class="workload-legend">
        Queue = assigned actions still open. Done = completed today (other vs Call). Updates = action updates today.
        Contact notes live in My day — Already in CRM.
    </p>
</section>

<div class="modal fade" id="workloadDrilldownModal" tabindex="-1" role="dialog" aria-labelledby="workloadDrilldownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workloadDrilldownModalLabel">Workload details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="workloadDrilldownLoading" class="text-center py-4" style="display:none;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="workloadDrilldownEmpty" class="text-muted py-3" style="display:none;">No items for today.</div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="workloadDrilldownTable" style="display:none;">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Person</th>
                                <th>Type</th>
                                <th>Class</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
