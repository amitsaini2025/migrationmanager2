@props(['items' => [], 'more' => 0])

<section class="my-day-card">
    <h3>Already in CRM</h3>
    <p class="my-day-lead">Do not log again — emails, docs, bookings, notes, completed actions.</p>
    <div id="myDayCrmList">
        @forelse($items as $item)
            <div class="my-day-crm-item" data-event-key="{{ $item['key'] ?? '' }}">
                <div class="my-day-crm-kind">{{ $item['kind'] ?? '' }}</div>
                <div>
                    <div class="my-day-crm-title">
                        @if(!empty($item['url']) && str_starts_with((string) ($item['key'] ?? ''), 'note:'))
                            <a href="{{ $item['url'] }}">{{ $item['title'] ?? '' }}</a>
                        @else
                            {{ $item['title'] ?? '' }}
                        @endif
                    </div>
                    @if(!empty($item['ref']))
                        <div class="my-day-crm-ref">
                            @if(!empty($item['url']))
                                <a href="{{ $item['url'] }}">{{ $item['ref'] }}</a>
                            @else
                                {{ $item['ref'] }}
                            @endif
                        </div>
                    @endif
                    @if(!empty($item['body']))
                        <div class="my-day-crm-body is-collapsed">
                            <div class="my-day-crm-body-text">{{ $item['body'] }}</div>
                            <button type="button" class="my-day-crm-show-more" aria-expanded="false">Show more</button>
                        </div>
                    @endif
                </div>
                <div class="my-day-crm-meta">
                    <span class="my-day-tag">{{ $item['time'] ?? '' }}</span>
                    @if(!empty($item['minutes']))
                        <span class="my-day-mins-chip">{{ (int) $item['minutes'] }}m</span>
                    @endif
                </div>
            </div>
        @empty
            <p class="my-day-empty">No CRM events logged by you today yet.</p>
        @endforelse
        @if($more > 0)
            <p class="my-day-more">… and {{ $more }} more</p>
        @endif
    </div>
</section>
