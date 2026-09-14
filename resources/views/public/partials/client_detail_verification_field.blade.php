@php
    $showNote = $showNote ?? false;
    $placeholder = $placeholder ?? '';
    $options = $options ?? [];
    $submitted = ! empty($submitted);
    $fieldResult = ($fieldResults ?? [])[$key] ?? null;
    $resultStatus = is_array($fieldResult) ? (string) ($fieldResult['status'] ?? '') : '';
    $requestedValue = is_array($fieldResult) ? trim((string) ($fieldResult['requested_value'] ?? '')) : '';
@endphp
<div class="field" data-key="{{ $key }}"
     @if($resultStatus === 'confirmed') data-status="confirmed"
     @elseif($resultStatus === 'change_requested') data-status="changed" data-new-value="{{ $requestedValue }}"
     @endif>
    <div class="field-top">
        <div>
            <div class="label">{{ $label }}</div>
            <div class="value current-value">{{ $value }}</div>
        </div>
        @unless($submitted)
        <div class="actions">
            <button type="button" class="btn-confirm" onclick="confirmField(this)">✓ Confirm</button>
            <button type="button" class="btn-change" onclick="openChange(this)">Request Change</button>
        </div>
        @endunless
    </div>
    <div class="edit-panel" @if($submitted) style="display:none" @endif>
        <div class="edit-grid">
            @if(($input ?? 'text') === 'select')
                <select class="new-value">
                    <option value="">Select</option>
                    @foreach($options as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            @elseif(($input ?? 'text') === 'address')
                <div class="address-entry-wrapper"
                     id="verificationAddressEntry"
                     data-search-route="{{ $addressSearchUrl }}"
                     data-details-route="{{ $addressDetailsUrl }}"
                     data-csrf-token="{{ csrf_token() }}">
                    <div class="form-group address-search-container">
                        <label for="verification_address_search">Search Address</label>
                        <input type="text"
                               id="verification_address_search"
                               class="address-search-input"
                               placeholder="Start typing an address..."
                               autocomplete="off"
                               data-address-index="0">
                    </div>
                    <div class="address-fields-grid">
                        <div class="form-group">
                            <label for="verification_address_line_1">Address Line 1 *</label>
                            <input type="text" id="verification_address_line_1" data-addr="line1" placeholder="Street number and name">
                        </div>
                        <div class="form-group">
                            <label for="verification_address_line_2">Address Line 2</label>
                            <input type="text" id="verification_address_line_2" data-addr="line2" placeholder="Apartment, suite, unit, etc.">
                        </div>
                    </div>
                    <div class="address-fields-grid">
                        <div class="form-group">
                            <label for="verification_address_suburb">Suburb *</label>
                            <input type="text" id="verification_address_suburb" data-addr="suburb" placeholder="Suburb">
                        </div>
                        <div class="form-group">
                            <label for="verification_address_state">State *</label>
                            <input type="text" id="verification_address_state" data-addr="state" placeholder="State">
                        </div>
                    </div>
                    <div class="address-fields-grid">
                        <div class="form-group">
                            <label for="verification_address_zip">Postcode *</label>
                            <input type="text" id="verification_address_zip" data-addr="zip" placeholder="Postcode">
                        </div>
                        <div class="form-group">
                            <label for="verification_address_country">Country *</label>
                            <input type="text" id="verification_address_country" data-addr="country" placeholder="Country" value="Australia">
                        </div>
                    </div>
                </div>
            @elseif(($input ?? 'text') === 'visa')
                <div class="visa-type-search-wrapper"
                     id="verificationVisaTypeEntry"
                     data-visa-types-route="{{ $visaTypesUrl }}"
                     data-csrf-token="{{ csrf_token() }}">
                    <label for="verification_visa_type_search">Visa Type / Subclass</label>
                    <div class="address-search-container">
                        <input type="text"
                               id="verification_visa_type_search"
                               class="visa-type-search-input"
                               placeholder="Start typing a visa type..."
                               autocomplete="off">
                    </div>
                </div>
            @elseif(($input ?? 'text') === 'textarea')
                <textarea class="new-value" placeholder="{{ $placeholder }}"></textarea>
            @else
                <input type="{{ $input }}" class="new-value" placeholder="{{ $placeholder }}" />
            @endif
            @if($showNote)
                <textarea class="change-note" placeholder="Optional note for our team"></textarea>
            @endif
        </div>
        <div class="edit-actions">
            <button type="button" class="btn-primary" onclick="saveChange(this)">Save Change Request</button>
            <button type="button" class="btn-secondary" onclick="cancelChange(this)">Cancel</button>
        </div>
    </div>
    <div class="status @if($resultStatus === 'confirmed') confirmed @elseif($resultStatus === 'change_requested') changed @endif">
        @if($resultStatus === 'confirmed')
            ✓ Confirmed as correct
        @elseif($resultStatus === 'change_requested')
            ⚠ Change requested: {{ $requestedValue }}
        @endif
    </div>
</div>
