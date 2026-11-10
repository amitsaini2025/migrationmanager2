@php
    use App\Services\EmailDeliveryStatus;
@endphp

<div class="email-event-timeline">
    <div class="email-event-item">
        <div class="email-event-icon email-event-icon--sent">
            @icon('fa-paper-plane')
        </div>
        <div class="email-event-body">
            <div class="email-event-label">Sent</div>
            <div class="email-event-time">{{ $sentAt->format('d M Y, H:i:s') }}</div>
        </div>
    </div>

    @foreach($events as $event)
        @php
            $metadata = is_array($event->metadata) ? $event->metadata : [];
            $device = EmailDeliveryStatus::summarizeUserAgent($metadata['useragent'] ?? null);
            $clickUrl = $metadata['url'] ?? null;
            $isSafeUrl = is_string($clickUrl) && preg_match('/^https?:\/\//i', $clickUrl);
        @endphp
        <div class="email-event-item">
            <div class="email-event-icon">
                {!! $event->iconHtml() !!}
            </div>
            <div class="email-event-body">
                <div class="email-event-label">{{ $event->label() }}</div>
                <div class="email-event-time">{{ $event->occurred_at->format('d M Y, H:i:s') }}</div>
                @if($isSafeUrl)
                    <div class="email-event-detail">
                        <a href="{{ e($clickUrl) }}" target="_blank" rel="noopener noreferrer">
                            {{ \Illuminate\Support\Str::limit($clickUrl, 80) }}
                        </a>
                    </div>
                @elseif(is_string($clickUrl) && $clickUrl !== '')
                    <div class="email-event-detail text-muted">{{ \Illuminate\Support\Str::limit($clickUrl, 80) }}</div>
                @endif
                @if($device)
                    <div class="email-event-detail text-muted">{{ $device }}</div>
                @endif
                @if(!empty($metadata['reason']) && in_array($event->event_type, ['bounced', 'dropped', 'deferred', 'blocked'], true))
                    <div class="email-event-detail email-event-detail--reason">{{ e($metadata['reason']) }}</div>
                @endif
            </div>
        </div>
    @endforeach

    @if($events->isEmpty())
        <div class="email-event-empty text-muted">
            <small>No delivery events recorded for this email.</small>
        </div>
    @endif
</div>
