@props([
    'sessions' => [],
])

@php
    $auto = $sessions['auto'] ?? [];
@endphp

<section class="my-day-card my-day-auto" id="myDayAutoSection">
    <h3>Time on files (auto)</h3>
    <p class="my-day-lead">Focused time while a client, lead, or company file tab was open. Editable minutes; posts to the feed when recorded.</p>
    <div id="myDayAutoList">
        @forelse ($auto as $row)
            <div class="my-day-auto-row" data-session-id="{{ $row['id'] }}">
                <div class="my-day-auto-ref">{{ $row['ref'] ?? '—' }}</div>
                <label class="my-day-auto-mins">
                    <input type="number" class="my-day-auto-mins-input" min="1" max="480" value="{{ $row['confirmed_minutes'] ?? 1 }}" aria-label="Minutes">
                    <span>m</span>
                </label>
                <div class="my-day-auto-meta">
                    @if (! empty($row['is_reviewed_only']))
                        reviewed file
                    @else
                        {{ (int) ($row['event_count'] ?? 0) }} activities
                    @endif
                    @if (! empty($row['posted']))
                        · posted
                    @endif
                </div>
                @if (empty($row['posted']))
                    <button type="button" class="my-day-auto-delete" data-session-id="{{ $row['id'] }}">Delete</button>
                @endif
            </div>
        @empty
            <p class="my-day-empty">No auto file time recorded yet today.</p>
        @endforelse
    </div>
</section>
