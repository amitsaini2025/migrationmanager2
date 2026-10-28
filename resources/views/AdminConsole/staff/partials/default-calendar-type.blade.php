@php
    $calendarTypes = collect(app(\App\Services\StaffPersonalCalendarFeedService::class)->calendarTypeOptions())
        ->pluck('label', 'key')
        ->all();
    $selectedCalendar = old('default_calendar_type', $selected ?? '');
    if ($selectedCalendar === 'paid') {
        $selectedCalendar = '';
    }
@endphp

<div class="form-group">
    <label for="default_calendar_type">Default website calendar</label>
    <select name="default_calendar_type" id="default_calendar_type" class="form-control">
        <option value="">Automatic (name/email, otherwise JRP)</option>
        @foreach($calendarTypes as $key => $label)
            <option value="{{ $key }}" @if((string) $selectedCalendar === (string) $key) selected @endif>{{ $label }}</option>
        @endforeach
    </select>
    <small class="text-muted d-block mt-1">Used on the CRM dashboard when this staff member has no named calendar of their own. Leave automatic unless they should open a specific calendar first.</small>
    @if ($errors->has('default_calendar_type'))
        <span class="custom-error" role="alert">
            <strong>{{ $errors->first('default_calendar_type') }}</strong>
        </span>
    @endif
</div>
