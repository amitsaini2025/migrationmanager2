@if(!empty($quickAccessEndsAt))
    <div id="crm-quick-access-timer" class="alert alert-info d-flex flex-wrap align-items-center justify-content-between mx-3 mt-3 mb-0" role="status" aria-live="polite">
        <span id="crm-quick-access-timer-text">15-minute access is on.</span>
    </div>
    <div id="crm-quick-access-warning" class="crm-quick-access-warning" hidden>
        <div class="crm-quick-access-warning-card" role="alertdialog" aria-modal="true" aria-labelledby="crm-quick-access-warning-title" aria-describedby="crm-quick-access-warning-body">
            <h5 id="crm-quick-access-warning-title" class="mb-2">Save your work</h5>
            <p id="crm-quick-access-warning-body" class="mb-3">This 15-minute access turns off in one minute. Save your work now. After that, access to this file will be off.</p>
            <button type="button" class="btn btn-warning" id="crm-quick-access-warning-ok">OK, I'll save</button>
        </div>
    </div>
    <style>
        #crm-quick-access-timer {
            position: sticky;
            top: 0;
            /* Keep below .main-topbar (1000) so global search .ts-dropdown stays above this bar.
               Keep above page content/footer (800). Do not use Bootstrap modal-backdrop (1040). */
            z-index: 900;
        }
        #crm-quick-access-timer.is-ending {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #664d03;
        }
        #crm-quick-access-timer.is-ended {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
        }
        .crm-quick-access-warning {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.45);
            padding: 1rem;
        }
        .crm-quick-access-warning[hidden] {
            display: none !important;
        }
        .crm-quick-access-warning-card {
            width: min(28rem, 100%);
            background: #fff;
            border-radius: 0.5rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.2);
        }
    </style>
    <script>
        (function () {
            var endsAt = Date.parse(@json($quickAccessEndsAt));
            var serverNow = Date.parse(@json(now('UTC')->toIso8601String()));
            var bar = document.getElementById('crm-quick-access-timer');
            var text = document.getElementById('crm-quick-access-timer-text');
            var dialog = document.getElementById('crm-quick-access-warning');
            if (!bar || !text || !dialog || Number.isNaN(endsAt)) {
                return;
            }
            var skew = Number.isNaN(serverNow) ? 0 : serverNow - Date.now();
            var warned = false;

            function remainingMs() {
                return endsAt - (Date.now() + skew);
            }

            function formatClock(ms) {
                var total = Math.max(0, Math.ceil(ms / 1000));
                var minutes = Math.floor(total / 60);
                var seconds = total % 60;
                return minutes + ':' + String(seconds).padStart(2, '0');
            }

            function showWarning() {
                if (warned) {
                    return;
                }
                warned = true;
                dialog.hidden = false;
                var ok = document.getElementById('crm-quick-access-warning-ok');
                if (ok) {
                    ok.focus();
                    ok.addEventListener('click', function () {
                        dialog.hidden = true;
                    });
                }
            }

            function tick() {
                var left = remainingMs();
                if (left <= 0) {
                    bar.classList.remove('alert-info', 'is-ending');
                    bar.classList.add('is-ended');
                    bar.setAttribute('aria-live', 'assertive');
                    text.textContent = '15-minute access is off. Save anything still open on this page. Further changes will not be kept.';
                    return;
                }
                if (left <= 60000) {
                    bar.classList.remove('alert-info');
                    bar.classList.add('is-ending');
                    bar.setAttribute('aria-live', 'assertive');
                    text.textContent = 'Save your work. Access turns off in ' + formatClock(left) + '.';
                    showWarning();
                } else {
                    text.textContent = '15-minute access ends in ' + formatClock(left) + '.';
                }
                window.setTimeout(tick, 1000);
            }

            tick();
        })();
    </script>
@endif
