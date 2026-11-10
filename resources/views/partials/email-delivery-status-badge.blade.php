@php
    $status = $status ?? 'pending';
    $label = \App\Services\EmailDeliveryStatus::statusLabel($status);
    $badgeClass = \App\Services\EmailDeliveryStatus::statusBadgeClass($status);
    $tip = $reason ?? null;
    if ($tip === null && $status === 'pending') {
        $tip = 'Waiting for Amazon SES to confirm delivery.';
    }
@endphp
<span class="badge {{ $badgeClass }} email-delivery-badge"
      @if($tip) data-toggle="tooltip" title="{{ e($tip) }}" @endif>{{ $label }}</span>
