{{-- Matter dropdown and status pill. Discontinued files stay in the list. --}}
<div class="sidebar-matter-selection">
    @if($fetchedData->type)
        @if(!empty($showMatterDropdown) && !empty($matter_list_arr))
            <select name="matter_id" id="sel_matter_id_client_detail" class="form-control mm-select visa-dropdown" data-valid="required">
                <option value="">Select Matters</option>
                @foreach($matter_list_arr as $matterlist)
                    @php
                        $matterName = 'General Matter';
                        if ($matterlist->sel_matter_id != 1 && !empty($matterlist->title)) {
                            $matterName = $matterlist->title;
                        }
                        $matterOptionLabel = $matterName.'('.($matterlist->client_unique_matter_no ?? '').')'
                            .\App\Support\ClientMatterSidebarStatus::optionSuffix((int) ($matterlist->matter_status ?? 1));
                    @endphp
                    <option value="{{ $matterlist->id }}" {{ $matterlist->id == ($latestClientMatterId ?? null) ? 'selected' : '' }} data-clientuniquematterno="{{ @$matterlist->client_unique_matter_no }}" data-sel-matter-id="{{ @$matterlist->sel_matter_id }}">{{ $matterOptionLabel }}</option>
                @endforeach
            </select>
        @endif
    @endif
</div>

<div class="matter-status-badge">{{ $matter_status_badge ?? 'N/A' }}</div>

@if(!empty($artAppealKind))
@php
    $artAppealLabel = ($artAppealOptions ?? [])[$artAppealCurrent ?? ''] ?? '';
@endphp
<div class="sidebar-art-appeal" style="margin-top: 10px;">
    <div style="font-size: 0.75rem; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Refused for</div>
    <div id="art_appeal_subject_saved" @if($artAppealLabel === '') style="display:none;" @endif>
        <div id="art_appeal_subject_label" style="font-size: 0.9rem; color: #111827; margin-bottom: 6px;">{{ $artAppealLabel }}</div>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="art_appeal_subject_edit">Edit</button>
    </div>
    <div id="art_appeal_subject_editor" @if($artAppealLabel !== '') style="display:none;" @endif>
        <select id="art_appeal_subject_detail" class="form-control form-control-sm">
            <option value="">Select</option>
            @foreach($artAppealOptions ?? [] as $val => $lbl)
                <option value="{{ $val }}" {{ (string) ($artAppealCurrent ?? '') === (string) $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn-sm btn-primary mt-2" id="art_appeal_subject_save">Save</button>
        <button type="button" class="btn btn-sm btn-link mt-2" id="art_appeal_subject_cancel" @if($artAppealLabel === '') style="display:none;" @endif>Cancel</button>
    </div>
    <div id="art_appeal_subject_msg" class="small mt-1"></div>
</div>
@push('scripts')
<script>
$(function () {
    var requiredMessage = @json($artAppealKind === 'company' ? 'Choose the company matter this ART is about.' : 'Choose the visa this ART is about.');
    var $saved = $('#art_appeal_subject_saved');
    var $editor = $('#art_appeal_subject_editor');
    var $select = $('#art_appeal_subject_detail');
    var $label = $('#art_appeal_subject_label');
    var $msg = $('#art_appeal_subject_msg');
    var $cancel = $('#art_appeal_subject_cancel');

    function showSaved(label) {
        $label.text(label);
        $saved.show();
        $editor.hide();
        $cancel.show();
        $select.data('saved-value', $select.val());
    }

    $('#art_appeal_subject_edit').on('click', function () {
        $msg.text('');
        $select.val($select.data('saved-value') || $select.val());
        $saved.hide();
        $editor.show();
    });

    $cancel.on('click', function () {
        $msg.text('');
        $select.val($select.data('saved-value') || '');
        $editor.hide();
        $saved.show();
    });

    $('#art_appeal_subject_save').on('click', function () {
        var value = $select.val();
        if (!value) {
            $msg.text(requiredMessage).css('color', '#b91c1c');
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: @json(route('clients.art-appeal-subject')),
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                client_id: @json((int) $fetchedData->id),
                client_matter_id: @json((int) ($latestClientMatterId ?? 0)),
                refused_visa_type: value
            },
            success: function (response) {
                $btn.prop('disabled', false);
                if (response.success) {
                    showSaved(response.refused_visa_type_label || $select.find('option:selected').text());
                    $msg.text('Saved').css('color', '#15803d');
                } else {
                    $msg.text(response.message || 'Could not save').css('color', '#b91c1c');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not save.';
                $msg.text(message).css('color', '#b91c1c');
            }
        });
    });

    if ($select.val()) {
        $select.data('saved-value', $select.val());
    }
});
</script>
@endpush
@endif
