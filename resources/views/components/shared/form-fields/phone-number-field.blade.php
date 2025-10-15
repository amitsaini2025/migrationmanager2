{{-- Shared Phone Number Field Component - Works for both Create and Edit modes --}}
@props(['index' => 0, 'contact' => null, 'mode' => 'create'])

<div class="repeatable-section">
    <button type="button" class="remove-item-btn" title="Remove Phone" onclick="removePhoneField(this)">
        <i class="fas fa-trash"></i>
    </button>
    
    {{-- Only include ID in edit mode --}}
    @if($mode === 'edit' && $contact?->id)
        <input type="hidden" name="contact_id[{{ $index }}]" value="{{ $contact->id }}">
    @endif
    
    <div class="content-grid">
        <div class="form-group">
            <label>Type</label>
            <select name="{{ $mode === 'edit' ? 'contact_type_hidden' : 'contact_type' }}[{{ $index }}]" class="contact-type-selector">
                <option value="Personal" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Personal' ? 'selected' : '' }}>Personal</option>
                <option value="Work" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Work' ? 'selected' : '' }}>Work</option>
                <option value="Mobile" {{ ($contact->contact_type ?? old("contact_type.$index", 'Mobile')) == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                <option value="Business" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Business' ? 'selected' : '' }}>Business</option>
                <option value="Secondary" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                <option value="Father" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Father' ? 'selected' : '' }}>Father</option>
                <option value="Mother" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Mother' ? 'selected' : '' }}>Mother</option>
                <option value="Brother" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Brother' ? 'selected' : '' }}>Brother</option>
                <option value="Sister" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Sister' ? 'selected' : '' }}>Sister</option>
                <option value="Uncle" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                <option value="Aunt" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                <option value="Cousin" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Cousin' ? 'selected' : '' }}>Cousin</option>
                <option value="Others" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Others' ? 'selected' : '' }}>Others</option>
                <option value="Partner" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Partner' ? 'selected' : '' }}>Partner</option>
                <option value="Not In Use" {{ ($contact->contact_type ?? old("contact_type.$index")) == 'Not In Use' ? 'selected' : '' }}>Not In Use</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Number</label>
            <div class="cus_field_input flex-container">
                <div class="country_code">
                    <select name="country_code[{{ $index }}]" class="country-code-input">
                        <option value="+61" {{ ($contact->country_code ?? old("country_code.$index", '+61')) == '+61' ? 'selected' : '' }}>🇦🇺 +61</option>
                        <option value="+91" {{ ($contact->country_code ?? old("country_code.$index")) == '+91' ? 'selected' : '' }}>🇮🇳 +91</option>
                        <option value="+1" {{ ($contact->country_code ?? old("country_code.$index")) == '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                        <option value="+44" {{ ($contact->country_code ?? old("country_code.$index")) == '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                        <option value="+49" {{ ($contact->country_code ?? old("country_code.$index")) == '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                        <option value="+33" {{ ($contact->country_code ?? old("country_code.$index")) == '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                        <option value="+86" {{ ($contact->country_code ?? old("country_code.$index")) == '+86' ? 'selected' : '' }}>🇨🇳 +86</option>
                        <option value="+81" {{ ($contact->country_code ?? old("country_code.$index")) == '+81' ? 'selected' : '' }}>🇯🇵 +81</option>
                        <option value="+82" {{ ($contact->country_code ?? old("country_code.$index")) == '+82' ? 'selected' : '' }}>🇰🇷 +82</option>
                        <option value="+65" {{ ($contact->country_code ?? old("country_code.$index")) == '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                        <option value="+60" {{ ($contact->country_code ?? old("country_code.$index")) == '+60' ? 'selected' : '' }}>🇲🇾 +60</option>
                        <option value="+66" {{ ($contact->country_code ?? old("country_code.$index")) == '+66' ? 'selected' : '' }}>🇹🇭 +66</option>
                        <option value="+63" {{ ($contact->country_code ?? old("country_code.$index")) == '+63' ? 'selected' : '' }}>🇵🇭 +63</option>
                        <option value="+84" {{ ($contact->country_code ?? old("country_code.$index")) == '+84' ? 'selected' : '' }}>🇻🇳 +84</option>
                        <option value="+62" {{ ($contact->country_code ?? old("country_code.$index")) == '+62' ? 'selected' : '' }}>🇮🇩 +62</option>
                    </select>
                </div>
                <input type="tel" 
                       name="phone[{{ $index }}]" 
                       value="{{ $contact->phone ?? old("phone.$index") }}" 
                       placeholder="Phone Number" 
                       class="phone-number-input phone-width" 
                       autocomplete="off">
            </div>
        </div>
    </div>
</div>

