@props([
    'sessions' => [],
    'deferred' => false,
])

@php
    $opened = $sessions['opened'] ?? [];
@endphp

<section class="my-day-card my-day-opened" id="myDayOpenedSection">
    <h3>Files opened <span class="my-day-opened-badge" id="myDayOpenedCount">{{ $deferred ? '…' : count($opened) }}</span></h3>
    <p class="my-day-lead">Opened today with no recorded work yet (&lt;2m, no CRM write).</p>
    <ul class="my-day-opened-list" id="myDayOpenedList">
        @if($deferred)
            <li class="dashboard-widget-loading">
                <div class="spinner spinner-sm"></div>
                <p>Loading opened files…</p>
            </li>
        @else
        @forelse ($opened as $row)
            <li>
                @if (! empty($row['url']))
                    <a href="{{ $row['url'] }}">{{ $row['ref'] ?? '—' }}</a>
                @else
                    {{ $row['ref'] ?? '—' }}
                @endif
                @if (! empty($row['minutes']))
                    <span class="my-day-opened-mins">{{ (int) $row['minutes'] }}m</span>
                @endif
            </li>
        @empty
            <li class="my-day-empty">No files opened without recorded time.</li>
        @endforelse
        @endif
    </ul>
</section>
