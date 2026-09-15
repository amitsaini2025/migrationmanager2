@extends('layouts.crm_client_detail')
@section('title', 'Staff Workload')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="server-error">
                @include('../Elements/flash-message')
            </div>
            <div class="row">
                <div class="col-3 col-md-3 col-lg-3">
                    @include('../Elements/CRM/setting')
                </div>
                <div class="col-9 col-md-9 col-lg-9">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <h4 class="mb-0">Staff Workload</h4>
                            <form method="get" action="{{ route('adminconsole.staff.workload') }}" class="form-inline">
                                <label for="workload_date" class="mr-2 mb-0">Date</label>
                                <input type="date" name="date" id="workload_date" class="form-control form-control-sm mr-2" value="{{ $selectedDate }}">
                                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                            </form>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">{{ $dateLabel }} ({{ config('app.timezone') }}) — active staff only. Pending is open queue as of today. My day is the same copy-summary staff see on the dashboard for that date (not stored; generated on open).</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Staff</th>
                                            <th>Completed<br><small>excl Call</small></th>
                                            <th>Updated</th>
                                            <th>Pending</th>
                                            <th>Call done</th>
                                            <th>Call notes</th>
                                            <th>In-person</th>
                                            <th>My day</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rows as $row)
                                            <tr>
                                                <td>{{ $row['name'] }}</td>
                                                @foreach(['completed_excl_call', 'updated', 'pending', 'call_completed', 'call_notes', 'in_person'] as $key)
                                                    @php $cell = $row[$key] ?? []; @endphp
                                                    <td>
                                                        <strong>{{ $cell['total'] ?? 0 }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $cell['clients'] ?? 0 }}c · {{ $cell['leads'] ?? 0 }}l
                                                            @if(($cell['personal'] ?? 0) > 0) · {{ $cell['personal'] }}p @endif
                                                        </small>
                                                        @if(($cell['new'] ?? 0) > 0 || ($cell['returning'] ?? 0) > 0)
                                                            <br>
                                                            <small>
                                                                @if(($cell['new'] ?? 0) > 0)<span class="text-primary">{{ $cell['new'] }} new</span>@endif
                                                                @if(($cell['returning'] ?? 0) > 0)<span class="text-warning">{{ $cell['returning'] }} ret</span>@endif
                                                            </small>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary js-staff-my-day"
                                                        data-url="{{ route('adminconsole.staff.workload.my-day', ['staff' => $row['staff_id'], 'date' => $selectedDate]) }}"
                                                        data-name="{{ e($row['name']) }}"
                                                    >Summary</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No active staff found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="staffMyDayModal" tabindex="-1" role="dialog" aria-labelledby="staffMyDayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staffMyDayModalLabel">My day</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="staffMyDayText" class="mb-0" style="white-space:pre-wrap;font-size:0.9rem;max-height:60vh;overflow:auto;">Loading…</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="staffMyDayCopy">Copy summary</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var modalEl = document.getElementById('staffMyDayModal');
    var titleEl = document.getElementById('staffMyDayModalLabel');
    var textEl = document.getElementById('staffMyDayText');
    var copyBtn = document.getElementById('staffMyDayCopy');
    var lastText = '';

    if (!modalEl || !titleEl || !textEl || !copyBtn) {
        return;
    }

    function showModal() {
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (window.jQuery) {
            jQuery(modalEl).modal('show');
        }
    }

    document.querySelectorAll('.js-staff-my-day').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-url');
            var name = btn.getAttribute('data-name') || 'Staff';
            titleEl.textContent = name + ' — My day';
            textEl.textContent = 'Loading…';
            lastText = '';
            showModal();
            fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(function (res) {
                return res.text().then(function (raw) {
                    var data = {};
                    try {
                        data = raw ? JSON.parse(raw) : {};
                    } catch (e) {
                        data = {};
                    }
                    if (!res.ok) {
                        throw new Error((data && data.message) || 'Could not load summary');
                    }
                    return data;
                });
            }).then(function (data) {
                lastText = (data.summary && data.summary.text) || '(none)';
                textEl.textContent = lastText;
            }).catch(function (err) {
                textEl.textContent = err && err.message ? err.message : 'Could not load summary';
            });
        });
    });

    copyBtn.addEventListener('click', function () {
        if (!lastText) {
            return;
        }
        var done = function () {
            var old = copyBtn.textContent;
            copyBtn.textContent = 'Copied';
            setTimeout(function () { copyBtn.textContent = old; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(lastText).then(done);
            return;
        }
        var ta = document.createElement('textarea');
        ta.value = lastText;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
        done();
    });
})();
</script>
@endpush
