@props(['items' => [], 'more' => 0])

<section class="my-day-card">
    <h3>Already in CRM — do not log again</h3>
    <p class="my-day-lead">Emails, documents, bookings, file notes, and completed actions from today.</p>
    <div id="myDayCrmList">
        @forelse($items as $item)
            <div class="my-day-crm-item">
                <div class="my-day-crm-kind">{{ $item['kind'] ?? '' }}</div>
                <div>
                    <div class="my-day-crm-title">{{ $item['title'] ?? '' }}</div>
                    @if(!empty($item['ref']))
                        <div class="my-day-crm-ref">{{ $item['ref'] }}</div>
                    @endif
                </div>
                <span class="my-day-tag">{{ $item['time'] ?? '' }}</span>
            </div>
        @empty
            <p class="my-day-empty">No CRM events logged by you today yet.</p>
        @endforelse
        @if($more > 0)
            <p class="my-day-more">… and {{ $more }} more</p>
        @endif
    </div>
</section>
