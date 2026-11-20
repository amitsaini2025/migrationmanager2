<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check In — Bansal Immigration</title>
    <style>
        :root { --navy:#1e3a5f; --blue:#2d6a9f; --bg:#f4f7fb; --muted:#5c6770; --line:#e4e8ee; --ok:#1b7f4a; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: #1a1a1a; line-height: 1.45; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 16px 12px 40px; }
        .card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 28px rgba(30,58,95,.08); border: 1px solid var(--line); }
        .head { background: linear-gradient(135deg, var(--navy), var(--blue)); color: #fff; padding: 20px 18px; }
        .head h1 { margin: 0; font-size: 1.25rem; }
        .head p { margin: 6px 0 0; opacity: .9; font-size: .92rem; }
        .body { padding: 18px 16px 22px; }
        .stepper { display: flex; gap: 4px; margin-bottom: 18px; }
        .step { flex: 1; text-align: center; min-width: 0; }
        .step .dot { width: 28px; height: 28px; border-radius: 50%; margin: 0 auto 4px; background: #d5dbe3; color: #667; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 700; position: relative; z-index: 1; }
        .step .lbl { font-size: .68rem; color: #7b8794; }
        .step.active .dot { background: var(--navy); color: #fff; }
        .step.active .lbl { color: var(--navy); font-weight: 700; }
        .step.done .dot { background: var(--ok); color: #fff; }
        .step.done .lbl { color: var(--ok); }
        .panel { display: none; }
        .panel.active { display: block; }
        h2 { margin: 0 0 12px; font-size: .78rem; letter-spacing: .04em; text-transform: uppercase; color: var(--navy); }
        label { display: block; font-weight: 650; font-size: .9rem; margin: 12px 0 6px; }
        .req { color: #b42318; }
        .hint { color: var(--muted); font-weight: 500; }
        input, select, textarea { width: 100%; font-size: 16px; padding: 12px 12px; border: 1px solid #cfd6df; border-radius: 10px; background: #fff; }
        textarea { min-height: 88px; resize: vertical; }
        input:focus, select:focus, textarea:focus { outline: 2px solid var(--blue); border-color: var(--blue); }
        .err { color: #b42318; font-size: .82rem; margin-top: 4px; min-height: 0; }
        .alert { display: none; background: #fdecea; color: #7a1f16; border-radius: 10px; padding: 10px 12px; margin-bottom: 12px; }
        .actions { display: flex; flex-direction: column; gap: 10px; margin-top: 18px; }
        button { font: inherit; cursor: pointer; border: 0; border-radius: 10px; padding: 14px 16px; font-weight: 700; min-height: 48px; }
        .primary { background: var(--navy); color: #fff; }
        .primary:disabled { opacity: .45; cursor: not-allowed; }
        .ghost { background: #eef2f6; color: #243140; }
        .yes { background: #e7f6ee; color: #0f6b3c; border: 2px solid #b7e4c7; }
        .no { background: #f4f6f8; color: #243140; border: 2px solid #d5dbe3; }
        .choices { display: flex; flex-direction: column; gap: 10px; }
        .choice.on { outline: 3px solid var(--navy); }
        .match, .appt { border: 2px solid var(--line); border-radius: 12px; padding: 14px; margin-bottom: 10px; cursor: pointer; }
        .match.on, .appt.on { border-color: var(--navy); background: #eef4fb; }
        .badge { display: inline-block; font-size: .72rem; font-weight: 700; border-radius: 999px; padding: 2px 8px; margin-left: 6px; }
        .badge.client { background: #e7f6ee; color: #0f6b3c; }
        .badge.lead { background: #fff6e0; color: #8a5a00; }
        .muted { color: var(--muted); font-size: .88rem; }
        .row { display: grid; grid-template-columns: 1fr; gap: 0 12px; }
        .summary { display: flex; flex-direction: column; gap: 4px; padding: 10px 0; border-bottom: 1px solid #f0f2f5; }
        .summary b { font-weight: 600; }
        .summary span { color: var(--muted); font-size: .82rem; }
        .center { text-align: center; padding: 28px 8px; }
        .spinner { display: none; text-align: center; color: var(--muted); margin-top: 12px; }
        .foot { text-align: center; color: var(--muted); font-size: .8rem; margin-top: 16px; }
        @media (min-width: 700px) {
            .wrap { padding: 36px 20px 56px; }
            .head, .body { padding-left: 28px; padding-right: 28px; }
            .head h1 { font-size: 1.45rem; }
            .step .lbl { font-size: .78rem; }
            .actions { flex-direction: row; justify-content: flex-end; }
            .actions button { width: auto; min-width: 140px; }
            .choices { flex-direction: row; }
            .choices button { flex: 1; }
            .row { grid-template-columns: 1fr 1fr; }
            .summary { flex-direction: row; justify-content: space-between; align-items: baseline; gap: 16px; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            <h1>Front-Desk Check-In</h1>
            <p>Tell us you have arrived. No login needed.</p>
        </div>
        <div class="body">
            <div class="stepper" id="stepper">
                <div class="step active" data-step="1"><div class="dot">1</div><div class="lbl">Contact</div></div>
                <div class="step" data-step="2"><div class="dot">2</div><div class="lbl">Match</div></div>
                <div class="step" data-step="3"><div class="dot">3</div><div class="lbl">Confirm</div></div>
                <div class="step" data-step="4"><div class="dot">4</div><div class="lbl">Appointment</div></div>
                <div class="step" data-step="5"><div class="dot">5</div><div class="lbl">Reason</div></div>
            </div>
            <div class="alert" id="alert" role="alert"></div>

            <section class="panel active" id="step-1">
                <h2>Step 1 — Contact details</h2>
                <label for="phone">Phone <span class="req">*</span></label>
                <input id="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" placeholder="e.g. 0412 345 678">
                <div class="err" id="phone-err"></div>
                <label for="email">Email <span class="hint">(optional)</span></label>
                <input id="email" type="email" inputmode="email" autocomplete="email" maxlength="255" placeholder="e.g. john@example.com">
                <div class="actions"><button type="button" class="primary" id="lookup">Look Up</button></div>
                <div class="spinner" id="lookup-spin">Looking up your details…</div>
            </section>

            <section class="panel" id="step-2">
                <h2>Step 2 — Is this you?</h2>
                <p class="muted" id="match-sub"></p>
                <div id="matches"></div>
                <button type="button" class="ghost" id="walkin" style="width:100%;margin-top:8px;">None of these — continue as a walk-in</button>
                <div class="actions">
                    <button type="button" class="ghost" id="back-2">Back</button>
                    <button type="button" class="primary" id="next-2" disabled>Continue</button>
                </div>
            </section>

            <section class="panel" id="step-new">
                <h2>We could not find that number</h2>
                <p>Are you visiting as a new client today?</p>
                <div class="choices">
                    <button type="button" class="yes" id="new-yes">Yes — I'm a new client</button>
                    <button type="button" class="no" id="new-no">No — I already have a file</button>
                </div>
                <div class="actions"><button type="button" class="ghost" id="back-new">Back</button></div>
            </section>

            <section class="panel" id="step-lead">
                <h2>Your details</h2>
                <div class="row">
                    <div>
                        <label for="first">First name <span class="req">*</span></label>
                        <input id="first" maxlength="70" autocomplete="given-name">
                        <div class="err" id="first-err"></div>
                    </div>
                    <div>
                        <label for="last">Last name <span class="req">*</span></label>
                        <input id="last" maxlength="70" autocomplete="family-name">
                        <div class="err" id="last-err"></div>
                    </div>
                    <div>
                        <label>Phone</label>
                        <input id="lead-phone" readonly>
                    </div>
                    <div>
                        <label>Email</label>
                        <input id="lead-email" readonly>
                    </div>
                    <div>
                        <label for="dob">Date of birth</label>
                        <input id="dob" type="date">
                    </div>
                    <div>
                        <label for="marital">Marital status</label>
                        <select id="marital">
                            <option value="">Select (optional)</option>
                            @foreach (['Single','Married','De facto','Separated / Divorced','Widowed'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="city">City / state / country</label>
                        <input id="city" maxlength="120" placeholder="e.g. Melbourne, VIC">
                    </div>
                    <div>
                        <label for="visa">Current Australian visa</label>
                        <select id="visa">
                            <option value="">Select (optional)</option>
                            @foreach (\App\Support\LeadMigrateInviteChannels::currentVisaOptions() as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="visa-exp">Visa expiry</label>
                        <input id="visa-exp" type="date">
                    </div>
                    <div>
                        <label for="qual">Highest qualification</label>
                        <select id="qual">
                            <option value="">Select (optional)</option>
                            @foreach (\App\Support\LeadMigrateInviteChannels::qualificationLevels() as $opt)
                                <option value="{{ $opt }}">{{ \App\Support\LeadMigrateInviteChannels::qualificationLevelLabel($opt) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="qual-date">Qualification date</label>
                        <input id="qual-date" type="date">
                    </div>
                    <div>
                        <label for="job">Occupation</label>
                        <input id="job" maxlength="130" placeholder="e.g. Chef, Accountant">
                    </div>
                </div>
                <label for="lead-reason">Reason for visit <span class="req">*</span></label>
                <select id="lead-reason">
                    <option value="">Select a reason</option>
                    @foreach ($visitReasons as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="err" id="lead-reason-err"></div>
                <label for="lead-notes">Notes <span class="req" id="lead-notes-req" style="display:none;">*</span></label>
                <textarea id="lead-notes" maxlength="2000"></textarea>
                <div class="err" id="lead-notes-err"></div>
                <div class="actions">
                    <button type="button" class="ghost" id="back-lead">Back</button>
                    <button type="button" class="primary" id="save-lead">Submit check-in</button>
                </div>
                <div class="spinner" id="lead-spin">Saving your check-in…</div>
            </section>

            <section class="panel" id="step-help">
                <div class="center">
                    <h2 style="font-size:1.1rem;text-transform:none;letter-spacing:0;">Please speak with our receptionist</h2>
                    <p class="muted">Our front-desk team will help you find your file.</p>
                    <button type="button" class="primary" id="help-restart">Start over</button>
                </div>
            </section>

            <section class="panel" id="step-3">
                <h2>Step 3 — Confirm your details</h2>
                <div id="summary"></div>
                <div class="actions">
                    <button type="button" class="ghost" id="back-3">Back</button>
                    <button type="button" class="primary" id="next-3">Details are correct</button>
                </div>
            </section>

            <section class="panel" id="step-4">
                <h2>Step 4 — Appointment</h2>
                <p>Do you have an appointment today?</p>
                <div class="choices">
                    <button type="button" class="yes" id="appt-yes">Yes, I have an appointment</button>
                    <button type="button" class="no" id="appt-no">No appointment</button>
                </div>
                <div id="appt-box" style="display:none;margin-top:14px;">
                    <div class="spinner" id="appt-spin" style="display:none;">Checking today's appointments…</div>
                    <div id="appts"></div>
                    <p class="muted" id="appt-none" style="display:none;">No appointment was found for today. You can still continue.</p>
                </div>
                <div class="actions">
                    <button type="button" class="ghost" id="back-4">Back</button>
                    <button type="button" class="primary" id="next-4" disabled>Continue</button>
                </div>
            </section>

            <section class="panel" id="step-5">
                <h2>Step 5 — Reason for your visit</h2>
                <label for="reason">Reason <span class="hint">(optional)</span></label>
                <select id="reason">
                    <option value="">Select a reason</option>
                    @foreach ($visitReasons as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label for="notes">Notes <span class="req" id="notes-req" style="display:none;">*</span></label>
                <textarea id="notes" maxlength="2000" placeholder="Anything we should know"></textarea>
                <div class="err" id="notes-err"></div>
                <div class="actions">
                    <button type="button" class="ghost" id="back-5">Back</button>
                    <button type="button" class="primary" id="save">Submit check-in</button>
                </div>
                <div class="spinner" id="save-spin">Saving your check-in…</div>
            </section>

            <section class="panel" id="step-done">
                <div class="center">
                    <h2 style="font-size:1.25rem;text-transform:none;letter-spacing:0;">You're checked in</h2>
                    <p id="done-msg"></p>
                    <button type="button" class="primary" id="again">New check-in</button>
                </div>
            </section>
        </div>
    </div>
    <p class="foot">Bansal Immigration</p>
</div>
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;
    var BASE = @json(url('/check-in'));
    var state = blank();
    var off = ['new', 'lead', 'help', 'done'];

    function blank() {
        return { phone:'', email:'', adminId:null, adminType:null, name:'', crmEmail:'', crmPhone:'', appointmentId:null, claimed:false };
    }
    function $(id) { return document.getElementById(id); }
    function show(id) { var el = $(id); if (el) el.style.display = 'block'; }
    function hide(id) { var el = $(id); if (el) el.style.display = 'none'; }
    function alertMsg(msg) { var el = $('alert'); el.textContent = msg; el.style.display = 'block'; el.scrollIntoView({ block:'nearest' }); }
    function clearAlert() { $('alert').style.display = 'none'; }
    function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }

    function go(step) {
        document.querySelectorAll('.panel').forEach(function (p) { p.classList.remove('active'); });
        var panel = $('step-' + step);
        if (panel) panel.classList.add('active');
        $('stepper').style.display = off.indexOf(step) === -1 ? '' : 'none';
        document.querySelectorAll('.step').forEach(function (node) {
            var n = parseInt(node.getAttribute('data-step'), 10);
            node.classList.remove('active', 'done');
            if (typeof step === 'number') {
                if (n < step) node.classList.add('done');
                if (n === step) node.classList.add('active');
            }
        });
        clearAlert();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function post(path, data) {
        return fetch(BASE + path, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With':'XMLHttpRequest' },
            body: JSON.stringify(data)
        }).then(function (r) {
            return r.text().then(function (text) {
                var j = {};
                try { j = text ? JSON.parse(text) : {}; } catch (e) { throw new Error('Something went wrong. Please try again.'); }
                if (!r.ok) {
                    var msg = j.message;
                    if (!msg && j.errors) msg = Object.values(j.errors).flat().join(' ');
                    throw new Error(msg || 'Something went wrong. Please try again.');
                }
                return j;
            });
        });
    }

    $('lookup').addEventListener('click', function () {
        var phone = $('phone').value.trim();
        if (phone.length < 6) { $('phone-err').textContent = 'Please enter a valid phone number.'; return; }
        $('phone-err').textContent = '';
        state.phone = phone;
        state.email = $('email').value.trim();
        $('lookup').disabled = true;
        show('lookup-spin');
        post('/lookup', { phone: state.phone, email: state.email || null }).then(function (data) {
            renderMatches(data.matches || []);
        }).catch(function (err) { alertMsg(err.message); }).finally(function () {
            $('lookup').disabled = false;
            hide('lookup-spin');
        });
    });
    $('phone').addEventListener('keydown', function (e) { if (e.key === 'Enter') $('lookup').click(); });

    function renderMatches(matches) {
        if (!matches.length) { go('new'); return; }
        $('match-sub').textContent = matches.length + (matches.length === 1 ? ' record found. Tap yours.' : ' records found. Tap yours.');
        var box = $('matches');
        box.innerHTML = '';
        $('next-2').disabled = true;
        $('walkin').classList.remove('on');
        matches.forEach(function (m) {
            var card = document.createElement('div');
            card.className = 'match';
            card.innerHTML = '<strong>' + esc(m.name || 'Unknown') + '</strong><span class="badge ' + (m.type === 'client' ? 'client' : 'lead') + '">' + esc(m.type) + '</span>'
                + (m.is_company && m.company_name ? '<span class="muted"> (' + esc(m.company_name) + ')</span>' : '')
                + '<br><span class="muted">' + esc(m.email || '—') + ' · ' + esc(m.phone || '—') + '</span>';
            card.addEventListener('click', function () {
                box.querySelectorAll('.match').forEach(function (c) { c.classList.remove('on'); });
                card.classList.add('on');
                $('walkin').classList.remove('on');
                state.adminId = parseInt(m.id, 10);
                state.adminType = m.type;
                state.name = m.name || '';
                state.crmEmail = m.email || '';
                state.crmPhone = m.phone || '';
                $('next-2').disabled = false;
            });
            box.appendChild(card);
        });
        go(2);
    }

    $('walkin').addEventListener('click', function () {
        document.querySelectorAll('.match').forEach(function (c) { c.classList.remove('on'); });
        state.adminId = null; state.adminType = null; state.name = 'Walk-in'; state.crmEmail = ''; state.crmPhone = '';
        $('walkin').classList.add('on');
        $('next-2').disabled = false;
    });
    $('back-2').addEventListener('click', function () { go(1); });
    $('next-2').addEventListener('click', function () { fillSummary(); go(3); });

    $('new-yes').addEventListener('click', function () {
        $('lead-phone').value = state.phone;
        $('lead-email').value = state.email || '—';
        ['first','last','dob','city','job','lead-notes'].forEach(function (id) { $(id).value = ''; });
        ['marital','visa','qual','lead-reason'].forEach(function (id) { $(id).value = ''; });
        $('visa-exp').value = ''; $('qual-date').value = '';
        ['first-err','last-err','lead-reason-err','lead-notes-err'].forEach(function (id) { $(id).textContent = ''; });
        hide('lead-notes-req');
        go('lead');
    });
    $('new-no').addEventListener('click', function () { go('help'); });
    $('back-new').addEventListener('click', function () { go(1); });
    $('back-lead').addEventListener('click', function () { go('new'); });
    $('help-restart').addEventListener('click', reset);
    $('lead-reason').addEventListener('change', function () {
        if (this.value === 'other') show('lead-notes-req'); else hide('lead-notes-req');
    });

    $('save-lead').addEventListener('click', function () {
        var ok = true;
        function need(id, errId, msg) {
            if (!$(id).value.trim()) { $(errId).textContent = msg; ok = false; } else { $(errId).textContent = ''; }
        }
        need('first', 'first-err', 'First name is required.');
        need('last', 'last-err', 'Last name is required.');
        if (!$('lead-reason').value) { $('lead-reason-err').textContent = 'Please select a reason.'; ok = false; }
        else $('lead-reason-err').textContent = '';
        if ($('lead-reason').value === 'other' && !$('lead-notes').value.trim()) {
            $('lead-notes-err').textContent = 'Please add a note when you choose Other.'; ok = false;
        } else $('lead-notes-err').textContent = '';
        if (!ok) return;
        $('save-lead').disabled = true;
        show('lead-spin');
        post('/create-lead', {
            first_name: $('first').value.trim(),
            last_name: $('last').value.trim(),
            phone: state.phone,
            email: state.email || null,
            dob: $('dob').value || null,
            marital_status: $('marital').value || null,
            city: $('city').value.trim() || null,
            current_visa: $('visa').value || null,
            visa_expiry: $('visa-exp').value || null,
            qualification: $('qual').value || null,
            qualification_date: $('qual-date').value || null,
            occupation: $('job').value.trim() || null,
            visit_reason: $('lead-reason').value,
            visit_notes: $('lead-notes').value.trim() || null
        }).then(function (data) {
            $('done-msg').textContent = data.message;
            go('done');
        }).catch(function (err) { alertMsg(err.message); }).finally(function () {
            $('save-lead').disabled = false;
            hide('lead-spin');
        });
    });

    function fillSummary() {
        var rows = [
            ['Phone', state.phone],
            ['Email', state.email || '—']
        ];
        if (state.adminId) {
            rows.push(['Name', state.name + ' (' + state.adminType + ')']);
            rows.push(['Email on file', state.crmEmail || '—']);
            rows.push(['Phone on file', state.crmPhone || '—']);
        } else {
            rows.push(['Record', 'Walk-in']);
        }
        $('summary').innerHTML = rows.map(function (r) {
            return '<div class="summary"><span>' + esc(r[0]) + '</span><b>' + esc(r[1]) + '</b></div>';
        }).join('');
    }
    $('next-3').addEventListener('click', function () {
        state.appointmentId = null; state.claimed = false;
        $('appt-yes').classList.remove('on'); $('appt-no').classList.remove('on');
        hide('appt-box'); $('appts').innerHTML = ''; $('next-4').disabled = true;
        go(4);
    });
    $('back-3').addEventListener('click', function () { go(2); });

    $('appt-no').addEventListener('click', function () {
        $('appt-no').classList.add('on'); $('appt-yes').classList.remove('on');
        state.claimed = false; state.appointmentId = null;
        hide('appt-box'); $('next-4').disabled = false;
    });
    $('appt-yes').addEventListener('click', function () {
        $('appt-yes').classList.add('on'); $('appt-no').classList.remove('on');
        state.claimed = true; state.appointmentId = null;
        show('appt-box'); $('next-4').disabled = true; $('appts').innerHTML = '';
        if (!state.adminId) {
            $('appts').innerHTML = '<p class="muted">We will still record your visit. There is no file to match an appointment to.</p>';
            hide('appt-spin'); hide('appt-none'); $('next-4').disabled = false;
            return;
        }
        show('appt-spin'); hide('appt-none');
        post('/appointments', { phone: state.phone, email: state.email || null, admin_id: state.adminId }).then(function (data) {
            hide('appt-spin');
            var list = data.appointments || [];
            if (!list.length) { show('appt-none'); $('next-4').disabled = false; return; }
            list.forEach(function (a) {
                var card = document.createElement('div');
                card.className = 'appt';
                card.innerHTML = '<strong>' + esc(a.datetime || '—') + '</strong> <span class="badge client">' + esc(a.status || '') + '</span><br><span class="muted">' + esc(a.consultant || 'Unassigned') + ' · ' + esc(a.location || '—') + '</span>';
                card.addEventListener('click', function () {
                    document.querySelectorAll('.appt').forEach(function (c) { c.classList.remove('on'); });
                    card.classList.add('on');
                    state.appointmentId = parseInt(a.id, 10);
                    $('next-4').disabled = false;
                });
                $('appts').appendChild(card);
            });
            $('next-4').disabled = false;
        }).catch(function (err) {
            hide('appt-spin');
            alertMsg(err.message);
            $('next-4').disabled = false;
        });
    });
    $('next-4').addEventListener('click', function () { go(5); });
    $('back-4').addEventListener('click', function () { go(3); });
    $('reason').addEventListener('change', function () {
        if (this.value === 'other') show('notes-req'); else { hide('notes-req'); $('notes-err').textContent = ''; }
    });
    $('save').addEventListener('click', function () {
        if ($('reason').value === 'other' && !$('notes').value.trim()) {
            $('notes-err').textContent = 'Please add a note when you choose Other.';
            return;
        }
        $('notes-err').textContent = '';
        $('save').disabled = true;
        show('save-spin');
        post('/submit', {
            phone: state.phone,
            email: state.email || null,
            admin_id: state.adminId,
            admin_type: state.adminType,
            appointment_id: state.appointmentId,
            claimed_appointment: state.claimed,
            visit_reason: $('reason').value || null,
            visit_notes: $('notes').value.trim() || null
        }).then(function (data) {
            $('done-msg').textContent = data.message;
            go('done');
        }).catch(function (err) { alertMsg(err.message); }).finally(function () {
            $('save').disabled = false;
            hide('save-spin');
        });
    });
    $('back-5').addEventListener('click', function () { go(4); });

    function reset() {
        state = blank();
        ['phone','email','reason','notes','first','last','lead-notes'].forEach(function (id) { if ($(id)) $(id).value = ''; });
        $('matches').innerHTML = ''; $('appts').innerHTML = '';
        $('next-2').disabled = true; $('next-4').disabled = true;
        go(1);
    }
    $('again').addEventListener('click', reset);
})();
</script>
</body>
</html>
