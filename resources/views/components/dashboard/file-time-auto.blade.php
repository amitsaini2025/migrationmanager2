@props([
    'sessions' => [],
])

@php
    $auto = $sessions['auto'] ?? [];
@endphp

<section class="my-day-card my-day-auto" id="myDayAutoSection">
    <h3>Time on files (auto)</h3>
    <p class="my-day-lead">Focused tab time on open files. Edit minutes; posts when recorded.</p>
    <div id="myDayAutoList">
        @forelse ($auto as $row)
            <div class="my-day-auto-row" data-session-id="{{ $row['id'] }}">
                <div class="my-day-auto-ref">
                    @if (! empty($row['url']))
                        <a href="{{ $row['url'] }}">{{ $row['ref'] ?? '—' }}</a>
                    @else
                        {{ $row['ref'] ?? '—' }}
                    @endif
                </div>
                <label class="my-day-auto-mins">
                    <input type="number" class="my-day-auto-mins-input" min="1" max="480" value="{{ $row['confirmed_minutes'] ?? 1 }}" aria-label="Minutes">
                    <span>m</span>
                </label>
                <div class="my-day-auto-meta">
                    @if (! empty($row['is_reviewed_only']))
                        reviewed file
                    @elseif ((int) ($row['event_count'] ?? 0) > 0)
                        <button
                            type="button"
                            class="my-day-auto-events-btn"
                            data-session-id="{{ $row['id'] }}"
                        >{{ (int) $row['event_count'] }} activities</button>
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
