@php
    $notUsedDocumentsTabFragmentUrl = route('clients.detail.notuseddocuments-tab', array_filter([
        'client_id' => $encodeId,
        'client_unique_matter_ref_no' => $id1 ?? null,
    ], static function ($value) {
        return $value !== null && $value !== '';
    }));
@endphp

{{-- Lightweight shell: full Not Used Documents UI loads on first open. --}}
<div class="tab-pane" id="notuseddocuments-tab"
     data-notuseddocuments-lazy="1"
     data-notuseddocuments-url="{{ $notUsedDocumentsTabFragmentUrl }}">
    <div class="card full-width documentalls-container">
        <x-client-detail-tab-loading label="Loading not used documents…" data-notuseddocuments-lazy-placeholder />
    </div>
</div>
