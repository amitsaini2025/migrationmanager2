@props([
    'hours' => [],
    'crmEvents' => [],
    'board' => [],
    'activityCounts' => [],
    'deferred' => false,
])

@php
    $hoursLabel = $deferred ? '…' : ($hours['label'] ?? '—');
    $eventItems = $crmEvents['items'] ?? [];
    $eventMore = (int) ($crmEvents['more'] ?? 0);
    $eventTotal = (int) ($crmEvents['total'] ?? (count($eventItems) + $eventMore));
    $entries = $board['entries'] ?? [];
    $tally = $board['tally'] ?? [];
    $byMatter = $board['by_matter'] ?? [];
    $sessions = $board['sessions'] ?? ['auto' => [], 'opened' => [], 'event_minutes' => []];
    $dateLabel = $board['date'] ?? ($hours['date'] ?? '');
@endphp

<section
    class="my-day{{ $deferred ? ' my-day--loading' : '' }}"
    id="myDay"
    aria-label="My day"
    @if($deferred) aria-busy="true" data-deferred="1" @endif
    data-initial-entries='@json($entries)'
    data-initial-tally='@json($tally)'
    data-initial-by-matter='@json($byMatter)'
    data-initial-sessions='@json($sessions)'
    data-initial-activity-counts='@json($activityCounts)'
>
    <div class="my-day-head">
        <div>
            <h2>My day</h2>
        </div>
        <div class="my-day-stamp">
            <span class="my-day-hours">Hours in CRM <b id="myDayHoursLabel">{{ $hoursLabel }}</b></span>
            <button type="button" class="my-day-add-btn" id="myDayAddBtn" aria-label="Add manual time log" title="Log time CRM cannot see">+</button>
        </div>
    </div>

    <x-dashboard.crm-events :items="$eventItems" :more="$eventMore" :total="$eventTotal" :deferred="$deferred" />

    <div class="my-day-split">
        <x-dashboard.file-time-auto :sessions="$sessions" :deferred="$deferred" />
        <x-dashboard.files-opened :sessions="$sessions" :deferred="$deferred" />
    </div>

    <x-dashboard.activity-counts :counts="$activityCounts" :deferred="$deferred" />

    <section class="my-day-card my-day-eod-wrap is-collapsed" id="myDayEodSection">
        <div class="my-day-eod-head">
            <button
                type="button"
                class="my-day-eod-toggle"
                id="myDayEodToggle"
                aria-expanded="false"
                aria-controls="myDayEodBody"
            >
                <span class="my-day-eod-chevron" aria-hidden="true"></span>
                <span class="my-day-eod-toggle-text">
                    <span class="my-day-eod-title">End-of-day summary</span>
                    <span class="my-day-lead">Copy for Teams. Manual logs are included.</span>
                </span>
            </button>
            <button type="button" class="my-day-add-btn" id="myDayAddBtnEod" aria-label="Add manual time log" title="Log time CRM cannot see">+</button>
        </div>

        <div class="my-day-eod-body" id="myDayEodBody" hidden>
            <div class="my-day-manual" id="myDayManualSection">
                <h4 class="my-day-manual-heading">Manual logs <span class="my-day-opened-badge" id="myDayManualCount">0</span></h4>
                <div class="my-day-manual-list" id="myDayManualList">
                    <p class="my-day-empty">No manual logs yet today.</p>
                </div>
            </div>

            <div class="my-day-still-open" id="myDayStillOpenSection">
                <h4 class="my-day-manual-heading">Still open <span class="my-day-opened-badge" id="myDayStillOpenCount">0</span></h4>
                <ul class="my-day-still-open-list" id="myDayStillOpenList">
                    <li class="my-day-empty">No files still open.</li>
                </ul>
            </div>

            <div class="my-day-eod" id="myDayEod" aria-live="polite">Loading summary…</div>
            <p class="my-day-hint" id="myDayEodSaved" hidden></p>
            <button type="button" class="my-day-btn" id="myDayCopyBtn">Copy summary</button>
        </div>
    </section>
</section>

<div class="modal fade" id="myDayLogModal" tabindex="-1" role="dialog" aria-labelledby="myDayLogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myDayLogModalLabel">Log time</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">For work the CRM cannot see. Minutes post to the matter feed when a file is linked.</p>
                <div class="my-day-capture">
                    <div class="my-day-picker">
                        <input type="text" id="myDayMatterSearch" class="form-control" placeholder="Matter ref, client, subclass…" autocomplete="off" aria-label="Search matter">
                        <div class="my-day-results" id="myDayResults" hidden></div>
                    </div>
                    <input type="text" id="myDayTitleInput" class="form-control" placeholder="What did you do?" aria-label="Title">
                    <input type="number" id="myDayLogMins" class="form-control" style="max-width:120px" min="1" max="480" value="15" aria-label="Minutes" placeholder="Minutes">
                </div>
                <div id="myDayChosenWrap"></div>
                <button type="button" class="my-day-admin-btn" id="myDayAdminBtn">No file — Admin / mailbox / internal</button>
                <div class="my-day-presets" id="myDayPresets" role="group" aria-label="Work type"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="myDayLogSave" disabled>Save log</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myDayAutoEventsModal" tabindex="-1" role="dialog" aria-labelledby="myDayAutoEventsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myDayAutoEventsModalLabel">Activities on this file</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-2" id="myDayAutoEventsRef"></p>
                <div id="myDayAutoEventsList" class="my-day-auto-events-list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
