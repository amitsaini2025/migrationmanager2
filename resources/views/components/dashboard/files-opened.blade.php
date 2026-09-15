@props([
    'sessions' => [],
])

@php
    $opened = $sessions['opened'] ?? [];
@endphp

<section class="my-day-card my-day-opened" id="myDayOpenedSection">
    <h3>Files opened <span class="my-day-opened-badge" id="myDayOpenedCount">{{ count($opened) }}</span></h3>
    <p class="my-day-lead">Opened today with no recorded work yet (&lt;2m, no CRM write).</p>
    <ul class="my-day-opened-list" id="myDayOpenedList">
        @forelse ($opened as $row)
            <li>
                {{ $row['ref'] ?? '—' }}
                @if (! empty($row['minutes']))
                    <span class="my-day-opened-mins">{{ (int) $row['minutes'] }}m</span>
                @endif
            </li>
        @empty
            <li class="my-day-empty">No files opened without recorded time.</li>
        @endforelse
    </ul>
</section>
