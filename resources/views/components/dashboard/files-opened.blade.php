@props([
    'sessions' => [],
    'deferred' => false,
])

@php
    $opened = $sessions['opened'] ?? [];
@endphp

<section class="my-day-card my-day-opened" id="myDayOpenedSection" data-dash-section="files-opened">
    <button type="button" class="dash-section-toggle" data-dash-toggle aria-expanded="true" aria-controls="myDayOpenedList">
        <span class="dash-section-chevron" aria-hidden="true"></span>
        <span class="dash-section-toggle-text">
            <h3>Files opened <span class="my-day-opened-badge" id="myDayOpenedCount">{{ $deferred ? '…' : count($opened) }}</span></h3>
            <p class="my-day-lead">Opened today with no recorded work yet (&lt;2m, no CRM write).</p>
        </span>
    </button>
    <ul class="my-day-opened-list dash-section-body" id="myDayOpenedList" data-dash-body>
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
