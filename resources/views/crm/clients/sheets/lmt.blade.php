@extends('layouts.crm_client_detail')
@section('title', 'Labour Market Testing')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
<link rel="stylesheet" href="{{ asset('css/listing-pagination.css') }}">
<style>
    .lmt-sheet-page .art-sheet-sticky-header {
        position: sticky; top: 0; z-index: 100;
        background: linear-gradient(180deg, #f0f7fa 0%, #e6f2f7 100%);
        border-bottom: 1px solid #b3d9ea;
    }
    .lmt-sheet-page .art-sheet-top-bar {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; padding: 10px 20px; flex-wrap: wrap;
    }
    .lmt-sheet-page .art-sheet-title { font-size: 1.2rem; font-weight: 600; color: #005792; margin: 0; }
    .lmt-status { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    .lmt-status-expired, .lmt-status-too_short { background: #f8d7da; color: #842029; }
    .lmt-status-incomplete { background: #fff3cd; color: #664d03; }
    .lmt-status-advertising, .lmt-status-not_started { background: #cfe2ff; color: #084298; }
    .lmt-status-ready { background: #d1e7dd; color: #0f5132; }
    .lmt-status-not_required, .lmt-status-not_recorded { background: #e9ecef; color: #495057; }
    .lmt-company-results { max-height: 180px; overflow: auto; border: 1px solid #ced4da; border-radius: 4px; }
    .lmt-company-results button { display: block; width: 100%; text-align: left; background: #fff; border: 0; border-bottom: 1px solid #eee; padding: 6px 10px; }
    .lmt-company-results button:hover { background: #f0f7fa; }
</style>
@endsection

@section('content')
<div class="listing-container lmt-sheet-page art-sheet-page">
    <section class="listing-section">
        <div class="listing-section-body">
            <div class="card art-sheet-card">
                <div class="art-sheet-sticky-header">
                    <div class="art-sheet-top-bar">
                        <h4 class="art-sheet-title">@icon('fa-clipboard-check') Labour Market Testing</h4>
                        <div style="display:flex; gap:8px;">
                            <button type="button" class="btn btn-primary btn-sm" id="lmtAddBtn">@icon('fa-plus') Add</button>
                            <a href="{{ route('clients.index') }}" class="btn btn-theme btn-theme-sm">@icon('fa-arrow-left') Back to Clients</a>
                        </div>
                    </div>
                    <form method="get" action="{{ route('clients.sheets.lmt') }}" class="d-flex flex-wrap align-items-center" style="gap:8px; padding: 0 20px 12px;">
                        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Company or matter" style="max-width: 240px;">
                        <select name="status" class="form-control" style="max-width: 180px;">
                            <option value="">All statuses</option>
                            @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="per_page" class="form-control" style="max-width: 110px;">
                            @foreach([10, 25, 50, 100] as $opt)
                                <option value="{{ $opt }}" {{ (int) $perPage === $opt ? 'selected' : '' }}>{{ $opt }}/page</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-theme btn-theme-sm">@icon('fa-filter') Filter</button>
                        <a href="{{ route('clients.sheets.lmt') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </form>
                </div>
                <div class="p-3">
                    <p class="text-muted small">Status uses today as the nomination lodgement day. It is calculated from LMT required and the dates. Advertisement copies are saved in the matter's LMT folder on the company page.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Matter</th>
                                    <th>Required</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Length</th>
                                    <th>Status</th>
                                    <th>Password</th>
                                    <th>Advertisements</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                    <tr>
                                        <td>
                                            @if($row->detail_url)
                                                <a href="{{ $row->detail_url }}">{{ $row->company_name }}</a>
                                            @else
                                                {{ $row->company_name }}
                                            @endif
                                        </td>
                                        <td>{{ $row->matter_label }}</td>
                                        <td>{{ $row->required_label }}</td>
                                        <td>{{ $row->start ?: '—' }}</td>
                                        <td>{{ $row->end ?: '—' }}</td>
                                        <td>{{ $row->span_days !== null ? $row->span_days.' days' : '—' }}</td>
                                        <td><span class="lmt-status lmt-status-{{ $row->status_key }}" title="{{ $row->status_detail }}">{{ $row->status_label }}</span></td>
                                        <td>{{ $row->password_set ? 'Set' : '—' }}</td>
                                        <td>
                                            @forelse($row->files as $file)
                                                @if($file['url'])
                                                    <a href="{{ $file['url'] }}" target="_blank" rel="noopener">{{ $file['name'] }}</a>
                                                @else
                                                    {{ $file['name'] }}
                                                @endif
                                                @if(! $loop->last)<br>@endif
                                            @empty
                                                —
                                            @endforelse
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary lmt-edit-btn" data-matter-id="{{ $row->matter_id }}">Edit</button>
                                        </td>
                                    </tr>
                                    @if($row->notes !== '')
                                        <tr>
                                            <td colspan="10" class="text-muted small">Notes: {{ $row->notes }}</td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">No Labour Market Testing records yet. Use Add to record one for a company matter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $rows->links() }}</div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="lmtSheetModal" tabindex="-1" role="dialog" aria-labelledby="lmtSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lmtSheetModalLabel">Labour Market Testing</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="text-danger small" id="lmtSheetError" style="display:none;"></p>
                <div class="form-group" id="lmtCompanyPicker">
                    <label for="lmtCompanySearch">Company</label>
                    <input type="search" id="lmtCompanySearch" class="form-control" placeholder="Type a company name" autocomplete="off">
                    <input type="hidden" id="lmtCompanyId">
                    <div class="lmt-company-results mt-1" id="lmtCompanyResults" style="display:none;"></div>
                    <p class="small mb-0 mt-1" id="lmtCompanyChosen"></p>
                </div>
                <div class="form-group">
                    <label for="lmtMatterId">Matter</label>
                    <select id="lmtMatterId" class="form-control">
                        <option value="">Select a company first</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lmtRequired">LMT required</label>
                    <select id="lmtRequired" class="form-control">
                        <option value="">Not set</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="lmtStartDate">Start date</label>
                        <input type="date" id="lmtStartDate" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="lmtEndDate">End date</label>
                        <input type="date" id="lmtEndDate" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="lmtNotes">Notes</label>
                    <textarea id="lmtNotes" class="form-control" rows="3" placeholder="Optional notes"></textarea>
                </div>
                <div class="form-group">
                    <label for="lmtPassword">Password</label>
                    <input type="text" id="lmtPassword" class="form-control" autocomplete="off" placeholder="LMT password">
                </div>
                <div class="form-group">
                    <label for="lmtAdvertisements">Advertisement copies</label>
                    <input type="file" id="lmtAdvertisements" class="form-control" multiple>
                    <p class="text-muted small mb-0">Saved into this matter's LMT folder. Up to 20MB each. File names may use letters, numbers, spaces, and . - _ $ ( ) , &amp; ' +</p>
                    <ul class="small mt-2 mb-0" id="lmtExistingFiles"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="lmtSaveBtn">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var modalEl = document.getElementById('lmtSheetModal');
    var companySearch = document.getElementById('lmtCompanySearch');
    var companyId = document.getElementById('lmtCompanyId');
    var companyResults = document.getElementById('lmtCompanyResults');
    var companyChosen = document.getElementById('lmtCompanyChosen');
    var matterSelect = document.getElementById('lmtMatterId');
    var errorEl = document.getElementById('lmtSheetError');
    var existingFiles = document.getElementById('lmtExistingFiles');
    var initialPassword = '';
    var editing = false;
    var searchTimer = null;

    function token() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showError(message) {
        errorEl.textContent = message || 'Could not save Labour Market Testing.';
        errorEl.style.display = 'block';
    }

    function clearError() {
        errorEl.style.display = 'none';
        errorEl.textContent = '';
    }

    function openModal() {
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            return;
        }
        if (window.jQuery) {
            window.jQuery(modalEl).modal('show');
        }
    }

    function hideModal() {
        if (window.bootstrap && window.bootstrap.Modal) {
            var instance = window.bootstrap.Modal.getInstance(modalEl);
            if (instance) {
                instance.hide();
            }
            return;
        }
        if (window.jQuery) {
            window.jQuery(modalEl).modal('hide');
        }
    }

    modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            hideModal();
        });
    });

    function resetForm() {
        editing = false;
        clearError();
        companySearch.disabled = false;
        companySearch.value = '';
        companyId.value = '';
        companyChosen.textContent = '';
        companyResults.style.display = 'none';
        companyResults.innerHTML = '';
        matterSelect.disabled = false;
        matterSelect.innerHTML = '<option value="">Select a company first</option>';
        document.getElementById('lmtRequired').value = '';
        document.getElementById('lmtStartDate').value = '';
        document.getElementById('lmtEndDate').value = '';
        document.getElementById('lmtNotes').value = '';
        document.getElementById('lmtPassword').value = '';
        document.getElementById('lmtAdvertisements').value = '';
        existingFiles.innerHTML = '';
        initialPassword = '';
        document.getElementById('lmtSheetModalLabel').textContent = 'Add Labour Market Testing';
    }

    function syncEndDate() {
        var start = document.getElementById('lmtStartDate');
        var end = document.getElementById('lmtEndDate');
        if (!start.value) return;
        var parts = start.value.split('-');
        if (parts.length !== 3) return;
        var date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        if (isNaN(date.getTime())) return;
        date.setDate(date.getDate() + 28);
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        end.value = date.getFullYear() + '-' + month + '-' + day;
    }

    function fillMatters(matters, selectedId) {
        matterSelect.innerHTML = '';
        if (!matters.length) {
            matterSelect.innerHTML = '<option value="">No active matters</option>';
            return;
        }
        matters.forEach(function (matter) {
            var option = document.createElement('option');
            option.value = String(matter.id);
            option.textContent = matter.label;
            matterSelect.appendChild(option);
        });
        if (selectedId) {
            matterSelect.value = String(selectedId);
        } else if (matters.length === 1) {
            matterSelect.value = String(matters[0].id);
        }
    }

    function loadMatters(selectedId) {
        if (!companyId.value) return Promise.resolve();
        return fetch('{{ url('/clients/sheets/lmt/companies') }}/' + encodeURIComponent(companyId.value) + '/matters', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (response) { return response.json(); })
          .then(function (data) { fillMatters(data.matters || [], selectedId); });
    }

    document.getElementById('lmtAddBtn').addEventListener('click', function () {
        resetForm();
        openModal();
    });

    document.getElementById('lmtStartDate').addEventListener('change', syncEndDate);

    companySearch.addEventListener('input', function () {
        companyId.value = '';
        companyChosen.textContent = '';
        clearTimeout(searchTimer);
        var term = companySearch.value.trim();
        if (term.length < 1) {
            companyResults.style.display = 'none';
            return;
        }
        searchTimer = setTimeout(function () {
            fetch('{{ route('clients.sheets.lmt.companies') }}?q=' + encodeURIComponent(term), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (response) {
                  if (!response.ok) {
                      throw new Error('search failed');
                  }
                  return response.json();
              })
              .then(function (data) {
                  companyResults.innerHTML = '';
                  (data.companies || []).forEach(function (company) {
                      var button = document.createElement('button');
                      button.type = 'button';
                      button.textContent = company.name;
                      button.addEventListener('click', function () {
                          companyId.value = String(company.id);
                          companySearch.value = company.name;
                          companyChosen.textContent = 'Selected: ' + company.name;
                          companyResults.style.display = 'none';
                          loadMatters(null);
                      });
                      companyResults.appendChild(button);
                  });
                  companyResults.style.display = companyResults.childElementCount ? 'block' : 'none';
              })
              .catch(function () {
                  companyResults.innerHTML = '';
                  companyResults.style.display = 'none';
              });
        }, 250);
    });

    document.querySelectorAll('.lmt-edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            resetForm();
            editing = true;
            document.getElementById('lmtSheetModalLabel').textContent = 'Edit Labour Market Testing';
            fetch('{{ url('/clients/sheets/lmt/matters') }}/' + encodeURIComponent(button.getAttribute('data-matter-id')), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (response) {
                if (!response.ok) throw new Error('Could not load this record.');
                return response.json();
            }).then(function (data) {
                companyId.value = String(data.company_id);
                companySearch.value = data.company_name;
                companySearch.disabled = true;
                companyChosen.textContent = 'Selected: ' + data.company_name;
                return loadMatters(data.matter_id).then(function () {
                    matterSelect.disabled = true;
                    document.getElementById('lmtRequired').value = data.lmt_required || '';
                    document.getElementById('lmtStartDate').value = data.lmt_start_date || '';
                    document.getElementById('lmtEndDate').value = data.lmt_end_date || '';
                    document.getElementById('lmtNotes').value = data.lmt_notes || '';
                    document.getElementById('lmtPassword').value = data.lmt_password || '';
                    initialPassword = data.lmt_password || '';
                    existingFiles.innerHTML = '';
                    (data.files || []).forEach(function (file) {
                        var item = document.createElement('li');
                        if (file.url) {
                            var link = document.createElement('a');
                            link.href = file.url;
                            link.target = '_blank';
                            link.rel = 'noopener';
                            link.textContent = file.name;
                            item.appendChild(link);
                        } else {
                            item.textContent = file.name;
                        }
                        existingFiles.appendChild(item);
                    });
                    openModal();
                });
            }).catch(function (error) {
                alert(error.message || 'Could not load this record.');
            });
        });
    });

    document.getElementById('lmtSaveBtn').addEventListener('click', function () {
        clearError();
        var password = document.getElementById('lmtPassword').value;
        if (password !== initialPassword && !window.confirm('Do you want to save/update the password?')) {
            return;
        }
        if (!companyId.value || !matterSelect.value) {
            showError('Choose a company and a matter.');
            return;
        }
        var body = new FormData();
        body.append('_token', token());
        body.append('company_id', companyId.value);
        body.append('client_matter_id', matterSelect.value);
        body.append('lmt_required', document.getElementById('lmtRequired').value);
        body.append('lmt_start_date', document.getElementById('lmtStartDate').value || '');
        body.append('lmt_end_date', document.getElementById('lmtEndDate').value || '');
        body.append('lmt_notes', document.getElementById('lmtNotes').value || '');
        body.append('lmt_password', password || '');
        Array.prototype.forEach.call(document.getElementById('lmtAdvertisements').files, function (file) {
            body.append('advertisements[]', file);
        });
        var saveBtn = document.getElementById('lmtSaveBtn');
        saveBtn.disabled = true;
        fetch('{{ route('clients.sheets.lmt.store') }}', {
            method: 'POST',
            body: body,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (response) {
            return response.text().then(function (text) {
                var data = {};
                try { data = text ? JSON.parse(text) : {}; } catch (e) { data = { message: 'Invalid server response' }; }
                return { ok: response.ok, data: data };
            });
        }).then(function (result) {
            saveBtn.disabled = false;
            if (result.ok && result.data.success) {
                window.location.reload();
                return;
            }
            showError(result.data.message || 'Could not save Labour Market Testing.');
        }).catch(function () {
            saveBtn.disabled = false;
            showError('Network error. Please try again.');
        });
    });
})();
</script>
@endsection
