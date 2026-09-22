@php
    $notesTabFragmentUrl = route('clients.detail.noteterm-tab', array_filter([
        'client_id' => $encodeId,
        'client_unique_matter_ref_no' => $id1 ?? null,
    ], static function ($value) {
        return $value !== null && $value !== '';
    }));
@endphp

{{-- Lightweight shell: full Notes UI loads on first open. --}}
<div class="tab-pane" id="noteterm-tab"
     data-noteterm-lazy="1"
     data-noteterm-url="{{ $notesTabFragmentUrl }}">
    <div class="card full-width notes-container">
        <x-client-detail-tab-loading label="Loading notes…" data-noteterm-lazy-placeholder />
    </div>
</div>
