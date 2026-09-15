@props([
    'hours' => [],
    'crmEvents' => [],
    'board' => [],
])

@php
    $hoursLabel = $hours['label'] ?? '—';
    $eventItems = $crmEvents['items'] ?? [];
    $eventMore = (int) ($crmEvents['more'] ?? 0);
    $entries = $board['entries'] ?? [];
    $tally = $board['tally'] ?? [];
    $byMatter = $board['by_matter'] ?? [];
    $dateLabel = $board['date'] ?? ($hours['date'] ?? '');
@endphp

<section
    class="my-day"
    id="myDay"
    aria-label="My day"
    data-initial-entries='@json($entries)'
    data-initial-tally='@json($tally)'
    data-initial-by-matter='@json($byMatter)'
>
    <div class="my-day-head">
        <div>
            <h2>My day</h2>
            <p class="my-day-sub">CRM events you already logged, plus optional time-on-file for work the CRM cannot see. Workload cards above stay CRM-only.</p>
        </div>
        <div class="my-day-stamp">
            <span class="my-day-hours">Hours in CRM <b id="myDayHoursLabel">{{ $hoursLabel }}</b> <span>· header only</span></span>
        </div>
    </div>

    <div class="my-day-split">
        <x-dashboard.crm-events :items="$eventItems" :more="$eventMore" />
        <x-dashboard.file-time-capture />
    </div>

    <x-dashboard.file-time-board />

    <div class="my-day-split my-day-split--bottom">
        <x-dashboard.file-time-by-matter :rows="$byMatter" />
        <div class="my-day-side">
            <x-dashboard.file-time-tally :tally="$tally" />
            <section class="my-day-card">
                <h3>End-of-day summary</h3>
                <p class="my-day-lead">CRM events + overlay time. Copy instead of rewriting the day in Teams.</p>
                <pre class="my-day-eod" id="myDayEod" aria-live="polite">Loading summary…</pre>
                <button type="button" class="my-day-btn" id="myDayCopyBtn">Copy summary</button>
                <p class="my-day-hint">Overlay posts to the matter feed automatically on Done. Admin / no file stays on My day only.</p>
            </section>
        </div>
    </div>
</section>

<div class="modal fade" id="myDayDoneModal" tabindex="-1" role="dialog" aria-labelledby="myDayDoneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myDayDoneModalLabel">How long did that take?</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="myDayDoneBlurb" class="text-muted mb-3">Clock time is a suggestion. Edit to what you actually spent.</p>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" id="myDayMinsInput" class="form-control" style="max-width:100px" min="1" max="480" aria-label="Minutes">
                    <span>minutes</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep working</button>
                <button type="button" class="btn btn-primary" id="myDayDoneOk">Done — log</button>
            </div>
        </div>
    </div>
</div>
