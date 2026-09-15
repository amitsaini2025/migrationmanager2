@props(['tally' => []])

@php
    $confirmed = (int) ($tally['confirmed_minutes'] ?? 0);
    $files = (int) ($tally['files_timed'] ?? 0);
    $open = (int) ($tally['still_open'] ?? 0);
    $admin = (int) ($tally['admin_minutes'] ?? 0);
    $byKind = $tally['by_kind'] ?? [];
@endphp

<section class="my-day-card">
    <h3>Today’s overlay</h3>
    <div class="my-day-kpis">
        <div class="my-day-kpi"><div class="n" id="myDayKTime">{{ $confirmed }}m</div><div class="l">Confirmed time</div></div>
        <div class="my-day-kpi"><div class="n" id="myDayKMatters">{{ $files }}</div><div class="l">Files timed</div></div>
        <div class="my-day-kpi"><div class="n" id="myDayKOpen">{{ $open }}</div><div class="l">Still open</div></div>
        <div class="my-day-kpi"><div class="n" id="myDayKAdmin">{{ $admin }}m</div><div class="l">Admin / no file</div></div>
    </div>
    <div class="my-day-bars" id="myDayBars" data-by-kind='@json($byKind)'></div>
    <p class="my-day-hint">Confirmed minutes at Done are what copy and post — not the live clock.</p>
</section>
