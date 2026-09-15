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
    $sessions = $board['sessions'] ?? ['auto' => [], 'opened' => [], 'event_minutes' => []];
    $dateLabel = $board['date'] ?? ($hours['date'] ?? '');
@endphp

<section
    class="my-day"
    id="myDay"
    aria-label="My day"
    data-initial-entries='@json($entries)'
    data-initial-tally='@json($tally)'
    data-initial-by-matter='@json($byMatter)'
    data-initial-sessions='@json($sessions)'
>
    <div class="my-day-head">
        <div>
            <h2>My day</h2>
            <p class="my-day-sub">Logged CRM work + auto file time. Queue above stays CRM-only.</p>
        </div>
        <div class="my-day-stamp">
            <span class="my-day-hours">Hours in CRM <b id="myDayHoursLabel">{{ $hoursLabel }}</b></span>
        </div>
    </div>

    <x-dashboard.crm-events :items="$eventItems" :more="$eventMore" />

    <div class="my-day-split">
        <x-dashboard.file-time-auto :sessions="$sessions" />
        <x-dashboard.files-opened :sessions="$sessions" />
    </div>

    <section class="my-day-card my-day-eod-wrap">
        <h3>End-of-day summary</h3>
        <p class="my-day-lead">Copy for Teams instead of rewriting the day. Copy also saves this date for admin review.</p>
        <pre class="my-day-eod" id="myDayEod" aria-live="polite">Loading summary…</pre>
        <p class="my-day-hint" id="myDayEodSaved" hidden></p>
        <button type="button" class="my-day-btn" id="myDayCopyBtn">Copy summary</button>
    </section>
</section>
