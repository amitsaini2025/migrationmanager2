@props([
    'label' => 'Loading…',
])

@once
<style>
    .client-detail-tab-loading {
        padding: 48px 24px;
        text-align: center;
        color: #6c757d;
    }
    .client-detail-tab-loading .client-detail-tab-loading__icon,
    .client-detail-tab-loading svg.client-detail-tab-loading__icon {
        display: block;
        width: 32px;
        height: 32px;
        margin: 0 auto 12px;
        color: #667eea;
    }
    .client-detail-tab-loading__label {
        margin: 0;
        font-weight: 500;
        font-size: 0.9375rem;
        color: #2d3748;
    }
</style>
@endonce

<div {{ $attributes->merge(['class' => 'workflow-v2-empty client-detail-tab-loading']) }}>
    @icon('fa-spinner', ['class' => 'client-detail-tab-loading__icon icon-spin', 'aria-hidden' => 'true'])
    <p class="client-detail-tab-loading__label mb-0">{{ $label }}</p>
</div>
