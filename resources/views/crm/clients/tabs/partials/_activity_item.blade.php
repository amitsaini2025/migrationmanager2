<li class="feed-item feed-item--email activity {{ $activity->activity_type ? 'activity-type-' . $activity->activity_type : '' }}" id="activity_{{ $activity->id }}">
    <span class="feed-icon {{ $activity->activity_type === 'sms' ? 'feed-icon-sms' : '' }} {{ $activity->activity_type === 'activity' ? 'feed-icon-activity' : '' }} {{ $activity->activity_type === 'financial' ? 'feed-icon-accounting' : '' }}">
        @if($activity->activity_type === 'sms')
            <i class="fas fa-sms"></i>
        @elseif($activity->activity_type === 'activity')
            <i class="fas fa-bolt"></i>
        @elseif($activity->activity_type === 'financial')
            <i class="fas fa-dollar-sign"></i>
        @elseif(str_contains(strtolower($activity->subject ?? ''), "invoice") || 
                str_contains(strtolower($activity->subject ?? ''), "receipt") || 
                str_contains(strtolower($activity->subject ?? ''), "ledger") || 
                str_contains(strtolower($activity->subject ?? ''), "payment") ||
                str_contains(strtolower($activity->subject ?? ''), "account"))
            <i class="fas fa-dollar-sign"></i>
        @elseif(str_contains($activity->subject ?? '', "document"))
            <i class="fas fa-file-alt"></i>
        @else
            <i class="fas fa-sticky-note"></i>
        @endif
    </span>
    <div class="feed-content">
        <p>
            <strong>{{ $admin->first_name ?? 'NA' }}  {{ $activity->subject ?? '' }}</strong>
            @if(str_contains($activity->subject ?? '', 'added a note') || str_contains($activity->subject ?? '', 'updated a note'))
                <i class="fas fa-ellipsis-v convert-activity-to-note" 
                   style="margin-left: 5px; cursor: pointer;" 
                   title="Convert to Note"
                   data-activity-id="{{ $activity->id }}"
                   data-activity-subject="{{ $activity->subject }}"
                   data-activity-description="{{ $activity->description }}"
                   data-activity-created-by="{{ $activity->created_by }}"
                   data-activity-created-at="{{ $activity->created_at }}"
                   data-client-id="{{ $clientId }}"></i>
            @endif
            -
            @if($activity->description != '')
                <p>{!! $activity->description !!}</p>
            @endif
        </p>
        <span class="feed-timestamp">{{ date('d M Y, H:i A', strtotime($activity->created_at)) }}</span>
    </div>
</li>

