<!doctype html>
<html lang="en-AU">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#114e92">
  <title>Migration Client Enquiry Form | Bansal Immigration Consultants</title>
  <style>
    :root{color-scheme:light;--blue:#0f4d93;--dark:#163355;--ink:#172a43;--muted:#65748a;--line:#d8e2ef;--bg:#eff4fa;--red:#b62e44}
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--ink);font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;font-size:15px;line-height:1.5}
    main{max-width:1000px;margin:30px auto 60px;padding:0 16px}
    .sheet{background:white;border:1px solid #dce6f2;box-shadow:0 10px 38px #1a426114;border-radius:16px;overflow:hidden}
    .hero{background:linear-gradient(115deg,#123d77,#155ea9);padding:32px 36px;color:white}
    .eyebrow{font-size:12px;letter-spacing:1.2px;font-weight:800;text-transform:uppercase;opacity:.92}
    h1{font-size:clamp(24px,4vw,32px);line-height:1.2;margin:8px 0 9px}
    .hero p{margin:0;color:#e7f1fd}
    form{padding:30px 36px 35px}
    section{margin:0 0 30px}
    .section-title{display:flex;align-items:center;gap:10px;border-bottom:1px solid var(--line);padding-bottom:11px;margin-bottom:18px}
    .number{background:#e8f2ff;color:var(--blue);width:30px;height:30px;display:grid;place-items:center;border-radius:9px;font-size:13px;font-weight:800;flex-shrink:0}
    h2{font-size:17px;color:var(--blue);margin:0}
    .fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 21px}
    .field{min-width:0}.wide{grid-column:1/-1}
    .fields>.field-left{grid-column:1}.fields>.field-right{grid-column:2}
    label{font-size:13px;display:block;margin-bottom:6px;font-weight:650}
    .required{color:var(--red)}
    input,select,textarea{display:block;width:100%;background:white;border:1px solid #c8d4e3;border-radius:8px;min-height:44px;padding:10px 12px;color:var(--ink);font:inherit;font-size:14px}
    input:disabled,input[readonly]{background:#f3f6fa;color:#4a5b70}
    input::placeholder,textarea::placeholder{color:#8592a5}
    textarea{min-height:104px;resize:vertical}
    input:focus,select:focus,textarea:focus,button:focus-visible{outline:3px solid #a4cfff;outline-offset:1px;border-color:#0f63b7}
    .help{display:block;color:var(--muted);font-size:12px;margin-top:5px}
    .note{padding:13px 16px;background:#f3f8ff;color:#536783;border-left:3px solid var(--blue);font-size:13px;border-radius:5px;margin-bottom:22px}
    .errors{padding:13px 16px;background:#fff5f5;color:#8a1f2f;border-left:3px solid var(--red);font-size:13px;border-radius:5px;margin-bottom:22px}
    .errors ul{margin:8px 0 0;padding-left:18px}
    .form-footer{border-top:1px solid var(--line);padding-top:20px}
    .privacy{font-size:12px;color:#5d6f86;margin:0 0 18px}
    .actions{display:flex;gap:10px;flex-wrap:wrap}
    button{background:var(--blue);border:1px solid var(--blue);color:white;padding:11px 17px;border-radius:8px;cursor:pointer;font:inherit;font-weight:700}
    button:hover{background:#0a3972}
    button.outline{background:white;color:var(--blue);border-color:#aac1db}button.outline:hover{background:#edf5ff}
    @media(max-width:650px){main{margin:12px auto 28px;padding:0 10px}.hero{padding:24px 20px}form{padding:24px 19px}.fields{grid-template-columns:1fr}.fields>.field-left,.fields>.field-right{grid-column:auto}.wide{grid-column:auto}.actions button{width:100%}}
  </style>
</head>
<body>
<main>
  <div class="sheet">
    <header class="hero">
      <div class="eyebrow">Bansal Immigration Consultants</div>
      <h1>Migration Client Enquiry Form</h1>
      <p>Please complete the details below so we can understand your migration enquiry.</p>
    </header>
    <form id="leadForm" method="POST" action="{{ $formAction }}">
      @csrf
      <div class="note"><strong>All fields are displayed below.</strong> Only fields marked <span class="required">*</span> are compulsory. Fill in other details if they apply to you.</div>

      @if ($errors->any())
        <div class="errors">
          <strong>Please fix the following:</strong>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <section>
        <div class="section-title"><span class="number">01</span><h2>Personal &amp; Contact Information</h2></div>
        <div class="fields">
          <div class="field">
            <label for="first_name">First Name <span class="required">*</span></label>
            <input id="first_name" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" maxlength="70" required placeholder="Enter first name">
          </div>
          <div class="field">
            <label for="last_name">Last Name <span class="required">*</span></label>
            <input id="last_name" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" maxlength="70" required placeholder="Enter last name">
          </div>
          <div class="field">
            <label for="phone">Phone / WhatsApp Number <span class="required">*</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', $prefillPhone) }}" autocomplete="tel" maxlength="25" required placeholder="e.g. 04XX XXX XXX">
          </div>
          <div class="field">
            <label for="email">Email Address <span class="required">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email', $prefillEmail) }}" autocomplete="email" maxlength="150" required placeholder="name@example.com">
          </div>
          <div class="field">
            <label for="dob">Date of Birth <span class="required">*</span></label>
            <input id="dob" name="dob" type="date" value="{{ old('dob') }}" required>
          </div>
          <div class="field">
            <label for="marital_status">Marital Status <span class="required">*</span></label>
            <select id="marital_status" name="marital_status" required>
              <option value="">Select</option>
              @foreach (['Single','Married','De facto','Separated / Divorced','Widowed'] as $opt)
                <option value="{{ $opt }}" @selected(old('marital_status') === $opt)>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="gender">Gender <span class="required">*</span></label>
            <select id="gender" name="gender" required>
              <option value="">Select</option>
              @foreach (['Male','Female','Other'] as $opt)
                <option value="{{ $opt }}" @selected(old('gender') === $opt)>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </section>

      <section>
        <div class="section-title"><span class="number">02</span><h2>Location &amp; Visa Information</h2></div>
        <div class="fields">
          <div class="field wide">
            <label for="address">Address</label>
            <input id="address" name="address" value="{{ old('address') }}" maxlength="255" autocomplete="street-address" placeholder="Street address">
          </div>
          <div class="field">
            <label for="city">City / State / Country</label>
            <input id="city" name="city" value="{{ old('city') }}" maxlength="120" placeholder="e.g. Melbourne, VIC">
          </div>
          <div class="field">
            <label for="current_visa">Current Australian Visa (if applicable)</label>
            <select id="current_visa" name="current_visa">
              <option value="">Select (optional)</option>
              @foreach (\App\Support\LeadMigrateInviteChannels::currentVisaOptions() as $opt)
                <option value="{{ $opt }}" @selected(old('current_visa') === $opt)>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="visa_expiry">Current Visa Expiry Date</label>
            <input id="visa_expiry" name="visa_expiry" type="date" value="{{ old('visa_expiry') }}">
          </div>
        </div>
      </section>

      <section>
        <div class="section-title"><span class="number">03</span><h2>Qualification</h2></div>
        <div class="fields">
          <div class="field field-left">
            <label for="qualification">Highest Completed Qualification</label>
            <select id="qualification" name="qualification">
              <option value="">Select (optional)</option>
              @foreach (\App\Support\LeadMigrateInviteChannels::qualificationLevels() as $opt)
                <option value="{{ $opt }}" @selected(old('qualification') === $opt)>{{ \App\Support\LeadMigrateInviteChannels::qualificationLevelLabel($opt) }}</option>
              @endforeach
            </select>
          </div>
          <div class="field field-left">
            <label for="qualification_type">Current Qualification Type</label>
            <select id="qualification_type" name="qualification_type">
              <option value="">Select (optional)</option>
              <option value="completed" @selected(old('qualification_type') === 'completed')>Completed</option>
              <option value="pursuing" @selected(old('qualification_type') === 'pursuing')>Pursuing</option>
            </select>
          </div>
          <div class="field field-right" id="qualification_date_wrap" style="{{ in_array(old('qualification_type'), ['completed', 'pursuing'], true) ? '' : 'display:none;' }}">
            <label for="qualification_date" id="qualification_date_label">
              {{ old('qualification_type') === 'pursuing' ? 'Completion Date' : 'Completed Date' }}
            </label>
            <input id="qualification_date" name="qualification_date" type="date" value="{{ old('qualification_date') }}">
          </div>
          <div class="field field-left">
            <label for="ielts_passed">Have you passed IELTS?</label>
            <select id="ielts_passed" name="ielts_passed">
              <option value="">Select (optional)</option>
              <option value="yes" @selected(old('ielts_passed') === 'yes')>Yes</option>
              <option value="no" @selected(old('ielts_passed') === 'no')>No</option>
            </select>
          </div>
          <div class="field field-right" id="ielts_score_wrap" style="{{ old('ielts_passed') === 'yes' ? '' : 'display:none;' }}">
            <label for="ielts_score">IELTS score</label>
            <input id="ielts_score" name="ielts_score" type="number" min="0" max="9" step="0.5" value="{{ old('ielts_score') }}" placeholder="e.g. 7.0">
          </div>
          <div class="field field-left">
            <label for="pte_passed">Have you passed PTE?</label>
            <select id="pte_passed" name="pte_passed">
              <option value="">Select (optional)</option>
              <option value="yes" @selected(old('pte_passed') === 'yes')>Yes</option>
              <option value="no" @selected(old('pte_passed') === 'no')>No</option>
            </select>
          </div>
          <div class="field field-right" id="pte_score_wrap" style="{{ old('pte_passed') === 'yes' ? '' : 'display:none;' }}">
            <label for="pte_score">PTE score</label>
            <input id="pte_score" name="pte_score" type="number" min="10" max="90" step="1" value="{{ old('pte_score') }}" placeholder="e.g. 65">
          </div>
        </div>
      </section>

      <section>
        <div class="section-title"><span class="number">04</span><h2>Work Experience</h2></div>
        <div class="fields">
          <div class="field wide">
            <label for="occupation">Current / Most Recent Occupation</label>
            <input id="occupation" name="occupation" value="{{ old('occupation') }}" maxlength="130" placeholder="e.g. Chef, Accountant, Engineer">
          </div>
          <div class="field field-left">
            <label for="has_job_experience">Have you any job experience?</label>
            <select id="has_job_experience" name="has_job_experience">
              <option value="">Select (optional)</option>
              <option value="yes" @selected(old('has_job_experience') === 'yes')>Yes</option>
              <option value="no" @selected(old('has_job_experience') === 'no')>No</option>
            </select>
          </div>
          <div class="field field-right" id="job_experience_years_wrap" style="{{ old('has_job_experience') === 'yes' ? '' : 'display:none;' }}">
            <label for="job_experience_years">No of Years</label>
            <input id="job_experience_years" name="job_experience_years" type="number" min="0" max="60" step="0.5" value="{{ old('job_experience_years') }}" placeholder="e.g. 3">
          </div>
        </div>
      </section>

      <section>
        <div class="section-title"><span class="number">05</span><h2>How did you hear about us?</h2></div>
        <div class="fields">
          <div class="field wide">
            <label for="source">Source <span class="required">*</span></label>
            <select id="source" name="source" required>
              <option value="">- Source -</option>
              @foreach (\App\Services\LeadEnquiryProfileService::heardAboutOptions() as $opt)
                <option value="{{ $opt }}" @selected(old('source') === $opt)>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </section>

      <div class="form-footer">
        <p class="privacy">Fields marked <span class="required">*</span> are compulsory. This link can only be used once.</p>
        <div class="actions">
          <button type="submit">Submit Enquiry</button>
        </div>
      </div>
    </form>
  </div>
</main>
<script>
(function () {
  function toggleScore(selectId, wrapId, inputId) {
    var select = document.getElementById(selectId);
    var wrap = document.getElementById(wrapId);
    var input = document.getElementById(inputId);
    if (!select || !wrap || !input) return;
    function sync() {
      var show = select.value === 'yes';
      wrap.style.display = show ? '' : 'none';
      if (!show) input.value = '';
    }
    select.addEventListener('change', sync);
    sync();
  }
  toggleScore('ielts_passed', 'ielts_score_wrap', 'ielts_score');
  toggleScore('pte_passed', 'pte_score_wrap', 'pte_score');
  toggleScore('has_job_experience', 'job_experience_years_wrap', 'job_experience_years');

  var qualType = document.getElementById('qualification_type');
  var qualDateWrap = document.getElementById('qualification_date_wrap');
  var qualDate = document.getElementById('qualification_date');
  var qualDateLabel = document.getElementById('qualification_date_label');
  if (qualType && qualDateWrap && qualDate && qualDateLabel) {
    function syncQualDate() {
      var type = qualType.value;
      if (type === 'completed' || type === 'pursuing') {
        qualDateWrap.style.display = '';
        qualDateLabel.textContent = type === 'pursuing' ? 'Completion Date' : 'Completed Date';
      } else {
        qualDateWrap.style.display = 'none';
        qualDate.value = '';
      }
    }
    qualType.addEventListener('change', syncQualDate);
    syncQualDate();
  }
})();
</script>
</body>
</html>
