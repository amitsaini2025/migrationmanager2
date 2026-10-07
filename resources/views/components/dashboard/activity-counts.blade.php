@props([
    'counts' => [],
    'deferred' => false,
])

@php
    $placeholder = $deferred ? '…' : null;
    $checklists = $placeholder ?? (int) ($counts['checklists'] ?? 0);
    $documents = $placeholder ?? (int) ($counts['documents'] ?? 0);
    $actions = $placeholder ?? (int) ($counts['actions'] ?? 0);
    $sms = $placeholder ?? (int) ($counts['sms'] ?? 0);
@endphp

<section class="my-day-card my-day-activity-counts" id="myDayActivityCounts" aria-label="No. of activities" data-dash-section="activity-counts">
    <button type="button" class="dash-section-toggle" data-dash-toggle aria-expanded="true" aria-controls="myDayActivityBody">
        <span class="dash-section-chevron" aria-hidden="true"></span>
        <span class="dash-section-toggle-text">
            <h3>No. of activities</h3>
            <p class="my-day-lead">Today’s totals for this staff member. Not limited by the list above.</p>
        </span>
    </button>
    <div class="my-day-kpis my-day-activity-kpis dash-section-body" id="myDayActivityBody" data-dash-body>
        <div class="my-day-kpi">
            <div class="l">Checklists sent</div>
            <div class="n" id="myDayCountChecklists">{{ $checklists }}</div>
        </div>
        <div class="my-day-kpi">
            <div class="l">Documents uploaded</div>
            <div class="n" id="myDayCountDocuments">{{ $documents }}</div>
        </div>
        <div class="my-day-kpi">
            <div class="l">Actions completed</div>
            <div class="n" id="myDayCountActions">{{ $actions }}</div>
        </div>
        <div class="my-day-kpi">
            <div class="l">SMS sent</div>
            <div class="n" id="myDayCountSms">{{ $sms }}</div>
        </div>
    </div>
</section>
