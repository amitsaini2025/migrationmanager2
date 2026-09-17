@props([
    'sessions' => [],
])

@php
    $auto = $sessions['auto'] ?? [];
@endphp

<section class="my-day-card my-day-auto" id="myDayAutoSection">
    <h3>Time on files (auto)</h3>
    <p class="my-day-lead">Focused tab time on open files. With multiple activities the box shows the average; click activities for each share. Edit posts the session total.</p>
    <div id="myDayAutoList">
        @forelse ($auto as $row)
            @php
                $totalMins = max(1, (int) ($row['confirmed_minutes'] ?? 1));
                $eventCount = (int) ($row['event_count'] ?? 0);
                if (! empty($row['events']) && is_array($row['events'])) {
                    $eventCount = max($eventCount, count($row['events']));
                }
                $showsAvg = empty($row['is_reviewed_only']) && $eventCount > 1 && $totalMins > 0;
                $displayMins = $showsAvg ? max(1, (int) round($totalMins / $eventCount)) : $totalMins;
            @endphp
            <div
                class="my-day-auto-row"
                data-session-id="{{ $row['id'] }}"
                data-total-minutes="{{ $totalMins }}"
                data-event-count="{{ $eventCount }}"
                data-shows-avg="{{ $showsAvg ? '1' : '0' }}"
            >
                <div class="my-day-auto-ref">
                    @if (! empty($row['url']))
                        <a href="{{ $row['url'] }}">{{ $row['ref'] ?? '—' }}</a>
                    @else
                        {{ $row['ref'] ?? '—' }}
                    @endif
                </div>
                <label class="my-day-auto-mins">
                    <input
                        type="number"
                        class="my-day-auto-mins-input"
                        min="1"
                        max="480"
                        value="{{ $displayMins }}"
                        aria-label="{{ $showsAvg ? 'Average minutes per activity' : 'Minutes' }}"
                        title="{{ $showsAvg ? 'Average per activity (session total '.$totalMins.'m). Editing sets average; total is average × activities.' : 'Session total minutes from focused tab time (min 1m when recorded)' }}"
                    >
                    <span>m</span>
                </label>
                <div class="my-day-auto-meta">
                    @if (! empty($row['is_reviewed_only']))
                        reviewed file
                    @elseif ($eventCount > 0)
                        <button
                            type="button"
                            class="my-day-auto-events-btn"
                            data-session-id="{{ $row['id'] }}"
                        >{{ $eventCount }} activities</button>
                    @else
                        {{ $eventCount }} activities
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
