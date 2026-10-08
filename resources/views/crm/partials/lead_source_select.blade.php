@php
    $fieldName = $name ?? 'source';
    $fieldId = $id ?? 'lead_source';
    $isRequired = $required ?? false;
    $rawSource = old($fieldName, $selectedValue ?? null);
    $selectedForDropdown = \App\Support\LeadSources::selectedValueForEdit($rawSource === '' ? null : $rawSource);
    $leadSourceOptions = \App\Support\LeadSources::options();
@endphp

<div class="form-group {{ $wrapperClass ?? '' }}">
    <label for="{{ $fieldId }}">Source @if($isRequired)<span class="text-danger">*</span>@endif</label>
    <select id="{{ $fieldId }}" name="{{ $fieldName }}" class="form-control" @if($isRequired) required @endif>
        <option value="">Select Source</option>
        @foreach($leadSourceOptions as $option)
            <option value="{{ $option }}" @selected($selectedForDropdown === $option)>{{ $option }}</option>
        @endforeach
    </select>
    @error($fieldName)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
