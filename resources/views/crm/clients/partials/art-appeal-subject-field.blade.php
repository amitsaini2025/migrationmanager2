@php
    $wrapId = $wrapId ?? 'art_appeal_subject_wrap';
    $fieldId = $fieldId ?? 'refused_visa_type';
@endphp
<div class="col-12 art-appeal-subject-wrap" id="{{ $wrapId }}" style="display:none;">
    <div class="form-group">
        <label for="{{ $fieldId }}">Refused for <span class="span_req">*</span></label>
        <select name="refused_visa_type" id="{{ $fieldId }}" class="form-control" disabled>
            <option value="">Select</option>
        </select>
        <small class="form-text text-muted art-appeal-subject-hint"></small>
    </div>
</div>
@once
    <script type="application/json" id="art-appeal-subject-options">@json(['person' => \App\Support\ArtAppealSubject::personOptions(), 'company' => \App\Support\ArtAppealSubject::companyOptions()])</script>
@endonce
