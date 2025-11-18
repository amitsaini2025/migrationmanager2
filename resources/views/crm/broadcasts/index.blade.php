@extends('layouts.crm_client_detail')
@section('title', 'Broadcast Notifications')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div>
                <h1 class="mb-0">Broadcast Notifications</h1>
                <p class="mb-0 broadcast-subtitle">Send announcements and monitor read receipts in real time.</p>
            </div>
        </div>

        <div class="section-body">
            <ul class="nav nav-tabs" id="broadcastTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="broadcasts-tab" data-toggle="tab" href="#broadcasts" role="tab" aria-controls="broadcasts" aria-selected="true">
                        <i class="fas fa-bullhorn mr-1"></i> Broadcasts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="active-users-tab" data-toggle="tab" href="#active-users" role="tab" aria-controls="active-users" aria-selected="false">
                        <i class="fas fa-users mr-1"></i> Active Users
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="broadcastTabsContent">
                <!-- Broadcasts Tab -->
                <div class="tab-pane fade show active" id="broadcasts" role="tabpanel" aria-labelledby="broadcasts-tab">
                    <div class="row mt-3">
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">Compose Broadcast</h4>
                                </div>
                                <div class="card-body">
                                    <div id="broadcast-compose-feedback" class="alert d-none" role="alert"></div>
                                    <form id="broadcast-compose-form" novalidate>
                                        @csrf
                                        <div class="form-group">
                                            <label for="broadcast-title">Title <span class="text-muted">(optional)</span></label>
                                            <input type="text" id="broadcast-title" name="title" class="form-control" maxlength="255" placeholder="System Maintenance">
                                        </div>

                                        <div class="form-group">
                                            <label for="broadcast-message">Message</label>
                                            <textarea id="broadcast-message" name="message" class="form-control" rows="5" maxlength="1000" placeholder="Enter the announcement you want everyone to see." required></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="broadcast-scope">Audience</label>
                                            <select id="broadcast-scope" name="scope" class="form-control">
                                                <option value="all" selected>All users</option>
                                                <option value="specific">Specific team members</option>
                                                <option value="team" disabled>Teams (coming soon)</option>
                                            </select>
                                        </div>

                                        <div class="form-group d-none" id="broadcast-recipient-group">
                                            <label for="broadcast-recipient-select">Select recipients</label>
                                            <select id="broadcast-recipient-select" class="form-control" name="recipient_ids[]" multiple="multiple" data-placeholder="Search team members"></select>
                                            <small class="form-text text-muted">Start typing to search for staff members. Portal user targeting will be added soon.</small>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <span class="text-muted small">
                                                Broadcasts send instantly and appear in the sticky banner for recipients.
                                            </span>
                                            <button type="submit" class="btn btn-primary" id="broadcast-submit-btn">
                                                <span class="submit-text">Send Broadcast</span>
                                                <span class="submit-spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">History</h4>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge badge-primary" id="broadcast-history-count">0 sent</span>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="broadcast-refresh-history">
                                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="broadcast-history-table">
                                            <thead>
                                                <tr>
                                                    <th>Sent</th>
                                                    <th>Message</th>
                                                    <th class="text-center">Read</th>
                                                    <th class="text-center">Unread</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="broadcast-history-body">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="fas fa-bullhorn mb-2" style="font-size: 28px;"></i>
                                                        <div>No broadcasts yet. Send your first announcement!</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer text-muted small">
                                    History is grouped by broadcast batch. Totals update automatically as recipients read notifications.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Users Tab -->
                <div class="tab-pane fade" id="active-users" role="tabpanel" aria-labelledby="active-users-tab">
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card" id="active-users-card">
                                <div class="card-header" style="background: linear-gradient(135deg, #5b9fd8 0%, #4285c9 100%) !important; border-bottom: none !important; padding: 1.5rem !important;">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                        <div>
                                            <h4 class="mb-0" style="color: #ffffff !important; font-weight: 700; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); font-size: 1.5rem;">Currently Active Users</h4>
                                            <small style="color: #ffffff !important; font-weight: 500; opacity: 0.95; font-size: 0.875rem;">Presence is calculated from active sessions within the last 5 minutes.</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('user-login-analytics.index') }}" class="btn btn-outline-light btn-sm" style="background-color: rgba(255, 255, 255, 0.2) !important; border: 2px solid rgba(255, 255, 255, 0.9) !important; color: #ffffff !important; font-weight: 600; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                <i class="fas fa-chart-line mr-1"></i> View Analytics
                                            </a>
                                            <span class="badge badge-success active-users-badge" id="active-users-count" style="background-color: #28a745 !important; color: #ffffff !important; border: 2px solid rgba(255, 255, 255, 0.95); font-weight: 700; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2); padding: 0.5rem 0.75rem; font-size: 0.875rem;">1 online</span>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="active-users-refresh" style="background-color: #ffffff !important; border: 2px solid rgba(255, 255, 255, 0.9) !important; color: #2c5aa0 !important; font-weight: 600; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Search and Filters -->
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" style="background-color: #ffffff !important; border: 2px solid rgba(255, 255, 255, 0.9) !important; border-right: 1px solid #dee2e6 !important; color: #495057 !important;"><i class="fas fa-search"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="active-users-search" placeholder="Search by name or email..." style="background-color: #ffffff !important; border: 2px solid rgba(255, 255, 255, 0.9) !important; border-left: 2px solid rgba(255, 255, 255, 0.9) !important; color: #212529 !important; font-weight: 500; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" id="active-users-role-filter" style="background-color: #ffffff !important; border: 2px solid rgba(255, 255, 255, 0.9) !important; color: #212529 !important; font-weight: 600; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                <option value="">All Roles</option>
                                                @foreach(\App\Models\UserRole::with('usertypedata')->get() as $role)
                                                    <option value="{{ $role->id }}">{{ $role->usertypedata->name ?? 'Role #' . $role->id }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" id="active-users-team-filter" style="background-color: #ffffff !important; border: 2px solid rgba(255, 255, 255, 0.9) !important; color: #212529!important; font-weight: 600; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                <option value="">All Teams</option>
                                                @foreach(\App\Models\Team::all() as $team)
                                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-secondary btn-block" id="active-users-clear-filters" style="background-color: #ffffff !important; border: 2px solid rgba(255, 255, 255, 0.9) !important; color: #2c5aa0 !important; font-weight: 700; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                                <i class="fas fa-times mr-1"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="active-users-table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="sortable" data-sort="name">
                                                        <i class="fas fa-user mr-1"></i> User
                                                        <i class="fas fa-sort sort-icon ml-1"></i>
                                                    </th>
                                                    <th class="text-center" style="width: 80px;">
                                                        <i class="fas fa-circle mr-1"></i> Status
                                                    </th>
                                                    <th class="sortable" data-sort="role">
                                                        <i class="fas fa-user-tag mr-1"></i> Role
                                                        <i class="fas fa-sort sort-icon ml-1"></i>
                                                    </th>
                                                    <th class="sortable" data-sort="team">
                                                        <i class="fas fa-users-cog mr-1"></i> Team
                                                        <i class="fas fa-sort sort-icon ml-1"></i>
                                                    </th>
                                                    <th class="sortable" data-sort="last_activity">
                                                        <i class="fas fa-clock mr-1"></i> Last Activity
                                                        <i class="fas fa-sort sort-icon ml-1"></i>
                                                    </th>
                                                    <th>
                                                        <i class="fas fa-sign-in-alt mr-1"></i> Last Login
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="active-users-body">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <div class="spinner-border spinner-border-sm text-primary mr-2" role="status" id="active-users-loading" style="display: none;">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <i class="fas fa-users mb-2" style="font-size: 28px;"></i>
                                                        <div id="active-users-empty-message">Click the tab to load active users.</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="text-muted small">
                                        <span id="active-users-info">Refreshing manually will recalculate active sessions in real time.</span>
                                        <span id="active-users-last-refresh" class="ml-2"></span>
                                    </div>
                                    <nav aria-label="Active users pagination" id="active-users-pagination">
                                        <!-- Pagination will be inserted here by JavaScript -->
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="broadcastDetailModal" tabindex="-1" role="dialog" aria-labelledby="broadcastDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="broadcastDetailModalLabel">Broadcast Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong id="broadcast-detail-title" class="d-block"></strong>
                    <span id="broadcast-detail-message" class="d-block"></span>
                    <small class="text-muted" id="broadcast-detail-meta"></small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Recipient</th>
                                <th>Status</th>
                                <th>Read at</th>
                            </tr>
                        </thead>
                        <tbody id="broadcast-detail-body">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Loading recipients…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const composeForm = document.getElementById('broadcast-compose-form');
        const messageInput = document.getElementById('broadcast-message');
        const titleInput = document.getElementById('broadcast-title');
        const scopeSelect = document.getElementById('broadcast-scope');
        const recipientGroup = document.getElementById('broadcast-recipient-group');
        const recipientSelect = $('#broadcast-recipient-select');

        const feedbackEl = document.getElementById('broadcast-compose-feedback');
        const submitBtn = document.getElementById('broadcast-submit-btn');
        const submitText = submitBtn.querySelector('.submit-text');
        const submitSpinner = submitBtn.querySelector('.submit-spinner');

        const historyBody = document.getElementById('broadcast-history-body');
        const historyCount = document.getElementById('broadcast-history-count');
        const refreshBtn = document.getElementById('broadcast-refresh-history');

        const detailModal = $('#broadcastDetailModal');
        const detailTitle = document.getElementById('broadcast-detail-title');
        const detailMessage = document.getElementById('broadcast-detail-message');
        const detailMeta = document.getElementById('broadcast-detail-meta');
        const detailBody = document.getElementById('broadcast-detail-body');

        const activeUsersBody = document.getElementById('active-users-body');
        const activeUsersCount = document.getElementById('active-users-count');
        const activeUsersRefresh = document.getElementById('active-users-refresh');
        const activeUsersSearch = document.getElementById('active-users-search');
        const activeUsersRoleFilter = document.getElementById('active-users-role-filter');
        const activeUsersTeamFilter = document.getElementById('active-users-team-filter');
        const activeUsersClearFilters = document.getElementById('active-users-clear-filters');
        const activeUsersLoading = document.getElementById('active-users-loading');
        const activeUsersEmptyMessage = document.getElementById('active-users-empty-message');
        const activeUsersLastRefresh = document.getElementById('active-users-last-refresh');
        const activeUsersPagination = document.getElementById('active-users-pagination');
        const activeUsersTab = document.getElementById('active-users-tab');

        // Active Users State
        let activeUsersState = {
            loaded: false,
            loading: false,
            currentPage: 1,
            perPage: 15,
            sortBy: 'name',
            sortDir: 'asc',
            search: '',
            roleId: null,
            teamId: null,
            refreshTimeout: null,
            debounceTimeout: null,
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function toggleRecipientsVisibility() {
            if (scopeSelect.value === 'specific') {
                recipientGroup.classList.remove('d-none');
            } else {
                recipientGroup.classList.add('d-none');
                recipientSelect.val(null).trigger('change');
            }
        }

        function showFeedback(type, message) {
            feedbackEl.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
            feedbackEl.classList.add(`alert-${type}`);
            feedbackEl.textContent = message;
        }

        function hideFeedback() {
            feedbackEl.classList.add('d-none');
            feedbackEl.textContent = '';
        }

        function setSubmitting(isSubmitting) {
            submitBtn.disabled = isSubmitting;
            submitText.classList.toggle('d-none', isSubmitting);
            submitSpinner.classList.toggle('d-none', !isSubmitting);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) {
                return '';
            }
            return new Intl.DateTimeFormat(undefined, {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
            }).format(date);
        }

        function renderHistoryTable(items) {
            historyBody.innerHTML = '';

            if (!items.length) {
                historyBody.innerHTML = `<tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-bullhorn mb-2" style="font-size: 28px;"></i>
                        <div>No broadcasts yet. Send your first announcement!</div>
                    </td>
                </tr>`;
                historyCount.textContent = '0 sent';
                return;
            }

            historyCount.textContent = `${items.length} sent`;

            items.forEach((item) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDate(item.sent_at)}</td>
                    <td>
                        ${item.title ? `<strong>${item.title}</strong><br>` : ''}
                        <span class="text-muted">${item.message}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-success">${item.read_count}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning">${item.unread_count}</span>
                    </td>
                    <td class="text-right">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-action="view-broadcast" data-batch="${item.batch_uuid}">
                            View details
                        </button>
                    </td>
                `;
                historyBody.appendChild(row);
            });
        }

        function formatTimeAgo(dateString) {
            if (!dateString) return '—';
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) return '—';
            
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
            
            return formatDate(dateString);
        }

        function getActivityDuration(lastActivity) {
            if (!lastActivity) return '—';
            const date = new Date(lastActivity);
            if (Number.isNaN(date.getTime())) return '—';
            
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''}`;
            const hours = Math.floor(diffMins / 60);
            return `${hours} hour${hours > 1 ? 's' : ''}`;
        }

        function getInitials(name) {
            if (!name) return '?';
            const parts = name.trim().split(/\s+/);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        }

        function renderActiveUsers(data, meta) {
            activeUsersBody.innerHTML = '';
            activeUsersLoading.style.display = 'none';

            if (!data || !data.length) {
                activeUsersBody.innerHTML = `<tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-users mb-2" style="font-size: 28px;"></i>
                        <div>No active users detected in the last few minutes.</div>
                    </td>
                </tr>`;
                activeUsersCount.textContent = '0 online';
                activeUsersCount.className = 'badge badge-secondary active-users-badge';
                activeUsersEmptyMessage.textContent = 'No active users detected in the last few minutes.';
                renderPagination(null);
                return;
            }

            const total = meta?.total || data.length;
            activeUsersCount.textContent = `${total} online`;
            activeUsersCount.className = 'badge badge-success active-users-badge';
            activeUsersEmptyMessage.textContent = '';

            data.forEach((user) => {
                const row = document.createElement('tr');
                row.className = 'active-user-row';
                
                const avatar = user.profile_img 
                    ? `<img src="${user.profile_img}" alt="${user.name}" class="user-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`
                    : '';
                const avatarFallback = `<div class="user-avatar-fallback" style="${user.profile_img ? 'display:none;' : ''}">${getInitials(user.name)}</div>`;
                
                const teamBadge = user.team_name 
                    ? `<span class="badge badge-light team-badge" ${user.team_color ? `style="background-color: ${user.team_color}20; color: ${user.team_color}; border: 1px solid ${user.team_color}40;"` : ''}>${user.team_name}</span>`
                    : '<span class="text-muted">—</span>';
                
                const roleName = user.role_name || `Role #${user.role_id || '—'}`;
                const officeInfo = user.office_name ? `<br><small class="text-muted"><i class="fas fa-building mr-1"></i>${user.office_name}</small>` : '';

                row.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar-wrapper mr-2">
                                ${avatar}
                                ${avatarFallback}
                            </div>
                            <div>
                                <strong>${user.name}</strong>
                                <br>
                                <span class="text-muted small">#${user.id}</span>
                                ${officeInfo}
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="status-indicator online" title="Online"></span>
                    </td>
                    <td>${roleName}</td>
                    <td>${teamBadge}</td>
                    <td>
                        <div>${formatTimeAgo(user.last_activity)}</div>
                        <small class="text-muted">Active for ${getActivityDuration(user.last_activity)}</small>
                    </td>
                    <td>${user.last_login ? formatTimeAgo(user.last_login) : '—'}</td>
                `;
                activeUsersBody.appendChild(row);
            });

            renderPagination(meta);
            updateSortIcons();
        }

        function renderPagination(meta) {
            if (!meta || meta.last_page <= 1) {
                activeUsersPagination.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';
            
            // Previous button
            html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${meta.current_page - 1}" ${meta.current_page === 1 ? 'tabindex="-1"' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>`;

            // Page numbers
            const startPage = Math.max(1, meta.current_page - 2);
            const endPage = Math.min(meta.last_page, meta.current_page + 2);

            if (startPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                if (startPage > 2) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            }

            if (endPage < meta.last_page) {
                if (endPage < meta.last_page - 1) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${meta.last_page}">${meta.last_page}</a></li>`;
            }

            // Next button
            html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${meta.current_page + 1}" ${meta.current_page === meta.last_page ? 'tabindex="-1"' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>`;

            html += '</ul>';
            activeUsersPagination.innerHTML = html;

            // Add click handlers
            activeUsersPagination.querySelectorAll('a[data-page]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.getAttribute('data-page'));
                    if (page && page !== activeUsersState.currentPage) {
                        activeUsersState.currentPage = page;
                        loadActiveUsers();
                    }
                });
            });
        }

        function updateSortIcons() {
            document.querySelectorAll('.sortable').forEach(th => {
                const sortIcon = th.querySelector('.sort-icon');
                if (!sortIcon) return;
                
                const sortValue = th.getAttribute('data-sort');
                if (sortValue === activeUsersState.sortBy) {
                    sortIcon.className = `fas fa-sort-${activeUsersState.sortDir === 'asc' ? 'up' : 'down'} sort-icon ml-1 text-primary`;
                } else {
                    sortIcon.className = 'fas fa-sort sort-icon ml-1 text-muted';
                }
            });
        }

        function loadHistory() {
            historyBody.classList.add('loading');
            fetch('/notifications/broadcasts/history', {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'include',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Unable to load broadcast history.');
                    }
                    return response.json();
                })
                .then((payload) => {
                    renderHistoryTable(payload.data || []);
                })
                .catch((error) => {
                    console.error(error);
                    showFeedback('danger', 'Failed to load broadcast history. Please try again.');
                })
                .finally(() => {
                    historyBody.classList.remove('loading');
                });
        }

        function loadBroadcastDetails(batchUuid) {
            detailBody.innerHTML = `<tr>
                <td colspan="3" class="text-center text-muted py-3">Loading recipients…</td>
            </tr>`;

            fetch(`/notifications/broadcasts/${batchUuid}/details`, {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'include',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Unable to load broadcast details.');
                    }
                    return response.json();
                })
                .then((payload) => {
                    const data = payload.data;
                    detailTitle.textContent = data.title || '';
                    detailTitle.classList.toggle('d-none', !data.title);
                    detailMessage.textContent = data.message || '';
                    detailMeta.textContent = `${data.sender_name || 'You'} • ${formatDate(data.sent_at)}`;

                    if (!Array.isArray(data.recipients) || !data.recipients.length) {
                        detailBody.innerHTML = `<tr>
                            <td colspan="3" class="text-center text-muted py-3">No recipients found.</td>
                        </tr>`;
                        return;
                    }

                    detailBody.innerHTML = '';
                    data.recipients.forEach((recipient) => {
                        const row = document.createElement('tr');
                        const statusBadge = recipient.read
                            ? '<span class="badge badge-success">Read</span>'
                            : '<span class="badge badge-secondary">Unread</span>';
                        row.innerHTML = `
                            <td>${recipient.receiver_name || `User #${recipient.receiver_id}`}</td>
                            <td>${statusBadge}</td>
                            <td>${recipient.read_at ? formatDate(recipient.read_at) : '-'}</td>
                        `;
                        detailBody.appendChild(row);
                    });
                })
                .catch((error) => {
                    console.error(error);
                    detailBody.innerHTML = `<tr>
                        <td colspan="3" class="text-center text-danger py-3">Failed to load recipients.</td>
                    </tr>`;
                });
        }

        function loadActiveUsers(showLoading = true) {
            if (activeUsersState.loading) return;
            
            activeUsersState.loading = true;
            if (showLoading) {
                activeUsersLoading.style.display = 'inline-block';
                activeUsersEmptyMessage.textContent = 'Loading active users...';
            }

            const params = new URLSearchParams({
                threshold: 5,
                page: activeUsersState.currentPage,
                per_page: activeUsersState.perPage,
                sort_by: activeUsersState.sortBy,
                sort_dir: activeUsersState.sortDir,
            });

            if (activeUsersState.search) {
                params.append('search', activeUsersState.search);
            }
            if (activeUsersState.roleId) {
                params.append('role_id', activeUsersState.roleId);
            }
            if (activeUsersState.teamId) {
                params.append('team_id', activeUsersState.teamId);
            }

            fetch(`/dashboard/active-users?${params.toString()}`, {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'include',
            })
                .then(async (response) => {
                    if (!response.ok) {
                        const error = await response.json().catch(() => ({}));
                        throw new Error(error.message || `HTTP ${response.status}: Unable to load active users.`);
                    }
                    return response.json();
                })
                .then((payload) => {
                    activeUsersState.loaded = true;
                    activeUsersState.loading = false;
                    renderActiveUsers(payload.data || [], payload.meta || {});
                    activeUsersLastRefresh.textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
                })
                .catch((error) => {
                    console.error('Active users load error:', error);
                    activeUsersState.loading = false;
                    activeUsersLoading.style.display = 'none';
                    activeUsersBody.innerHTML = `<tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            <i class="fas fa-exclamation-triangle mb-2"></i>
                            <div><strong>Failed to load active users</strong></div>
                            <div class="small mt-2">${error.message || 'Please try again or refresh the page.'}</div>
                            <button class="btn btn-sm btn-outline-primary mt-2" id="active-users-retry-btn">
                                <i class="fas fa-redo mr-1"></i> Retry
                            </button>
                        </td>
                    </tr>`;
                    activeUsersCount.textContent = 'Unavailable';
                    activeUsersCount.className = 'badge badge-warning active-users-badge';
                    
                    // Add retry button handler
                    const retryBtn = activeUsersBody.querySelector('#active-users-retry-btn');
                    if (retryBtn) {
                        retryBtn.addEventListener('click', () => loadActiveUsers(true));
                    }
                });
        }

        function debounceLoadActiveUsers(delay = 300) {
            clearTimeout(activeUsersState.debounceTimeout);
            activeUsersState.debounceTimeout = setTimeout(() => {
                activeUsersState.currentPage = 1; // Reset to first page on filter change
                loadActiveUsers();
            }, delay);
        }

        // Tab-based loading
        if (activeUsersTab) {
            // Use jQuery for Bootstrap tab events
            $('#active-users-tab').on('shown.bs.tab', function() {
                if (!activeUsersState.loaded && !activeUsersState.loading) {
                    loadActiveUsers();
                }
            });
        }

        composeForm.addEventListener('submit', (event) => {
            event.preventDefault();
            hideFeedback();

            if (!messageInput.value.trim()) {
                showFeedback('warning', 'Please enter a message before sending your broadcast.');
                messageInput.focus();
                return;
            }

            if (scopeSelect.value === 'specific' && recipientSelect.val().length === 0) {
                showFeedback('warning', 'Select at least one recipient or switch back to All users.');
                return;
            }

            setSubmitting(true);

            fetch('/notifications/broadcasts/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'include',
                body: JSON.stringify({
                    title: titleInput.value || null,
                    message: messageInput.value,
                    scope: scopeSelect.value,
                    recipient_ids: scopeSelect.value === 'specific' ? recipientSelect.val() : [],
                }),
            })
                .then(async (response) => {
                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Unable to send broadcast.');
                    }
                    return payload;
                })
                .then((payload) => {
                    showFeedback('success', 'Broadcast sent successfully.');
                    composeForm.reset();
                    recipientSelect.val(null).trigger('change');
                    toggleRecipientsVisibility();
                    loadHistory();
                })
                .catch((error) => {
                    console.error(error);
                    showFeedback('danger', error.message || 'Failed to send broadcast.');
                })
                .finally(() => {
                    setSubmitting(false);
                });
        });

        historyBody.addEventListener('click', (event) => {
            const button = event.target.closest('[data-action="view-broadcast"]');
            if (!button) {
                return;
            }
            const batchUuid = button.getAttribute('data-batch');
            if (!batchUuid) {
                return;
            }
            loadBroadcastDetails(batchUuid);
            detailModal.modal('show');
        });

        refreshBtn.addEventListener('click', (event) => {
            event.preventDefault();
            loadHistory();
        });

        // Active Users Event Listeners
        if (activeUsersRefresh) {
            activeUsersRefresh.addEventListener('click', (event) => {
                event.preventDefault();
                if (activeUsersState.loading) return;
                loadActiveUsers(true);
            });
        }

        if (activeUsersSearch) {
            activeUsersSearch.addEventListener('input', (e) => {
                activeUsersState.search = e.target.value.trim();
                debounceLoadActiveUsers(500);
            });
        }

        if (activeUsersRoleFilter) {
            activeUsersRoleFilter.addEventListener('change', (e) => {
                activeUsersState.roleId = e.target.value ? parseInt(e.target.value) : null;
                debounceLoadActiveUsers();
            });
        }

        if (activeUsersTeamFilter) {
            activeUsersTeamFilter.addEventListener('change', (e) => {
                activeUsersState.teamId = e.target.value ? parseInt(e.target.value) : null;
                debounceLoadActiveUsers();
            });
        }

        if (activeUsersClearFilters) {
            activeUsersClearFilters.addEventListener('click', (e) => {
                e.preventDefault();
                activeUsersState.search = '';
                activeUsersState.roleId = null;
                activeUsersState.teamId = null;
                activeUsersState.currentPage = 1;
                if (activeUsersSearch) activeUsersSearch.value = '';
                if (activeUsersRoleFilter) activeUsersRoleFilter.value = '';
                if (activeUsersTeamFilter) activeUsersTeamFilter.value = '';
                loadActiveUsers();
            });
        }

        // Sortable columns
        document.querySelectorAll('.sortable').forEach(th => {
            th.style.cursor = 'pointer';
            th.addEventListener('click', () => {
                const sortValue = th.getAttribute('data-sort');
                if (sortValue === activeUsersState.sortBy) {
                    activeUsersState.sortDir = activeUsersState.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    activeUsersState.sortBy = sortValue;
                    activeUsersState.sortDir = 'asc';
                }
                loadActiveUsers();
            });
        });

        scopeSelect.addEventListener('change', toggleRecipientsVisibility);

        recipientSelect.select2({
            width: '100%',
            placeholder: recipientSelect.data('placeholder') || 'Select recipients',
            minimumInputLength: 1,
            ajax: {
                url: '/getassigneeajax',
                dataType: 'json',
                delay: 250,
                data(params) {
                    return {
                        likevalue: params.term || '',
                    };
                },
                processResults(data) {
                    return {
                        results: (data || []).map((item) => ({
                            id: item.id,
                            text: item.assignee || item.agent_id || `User #${item.id}`,
                        })),
                    };
                },
                cache: true,
            },
        });

        toggleRecipientsVisibility();
        loadHistory();
        // Active users will load when tab is clicked (tab-based loading)
    })();
</script>
@endpush

@push('styles')
<style>
    .broadcast-subtitle {
        color: #4a5568;
    }

     /* Active Users Styling */
    .active-users-badge {
        font-size: 0.875rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
    }

    /* Header Section Improvements */
    #active-users .card > .card-header {
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
        border-bottom: none !important;
        padding: 1.5rem !important;
    }

    #active-users .card-header h4 {
        color: #ffffff !important;
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    #active-users .card-header small {
        color: rgba(255, 255, 255, 0.95) !important;
        font-weight: 400;
    }

    #active-users .card-header .text-muted {
        color: rgba(255, 255, 255, 0.95) !important;
    }

    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #28a745;
        box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.1);
        }
    }

    .status-indicator.online {
        background-color: #28a745;
        box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
    }

    .user-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .user-avatar,
    .user-avatar-fallback {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-avatar-fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .active-user-row {
        transition: background-color 0.2s ease;
    }

    .active-user-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .sortable {
        user-select: none;
        position: relative;
    }

    .sortable:hover {
        background-color: #f8f9fa;
    }

    .sort-icon {
        font-size: 0.75rem;
        opacity: 0.5;
        transition: opacity 0.2s ease;
    }

    .sortable:hover .sort-icon {
        opacity: 1;
    }

    .team-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .active-users-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .user-avatar,
        .user-avatar-fallback {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        #active-users-table th,
        #active-users-table td {
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        #active-users-table th {
            font-size: 0.75rem;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
        }
    }

    /* Pagination Styling */
    #active-users-pagination .pagination {
        margin-bottom: 0;
    }

    #active-users-pagination .page-link {
        color: #667eea;
        border-color: #dee2e6;
    }

    #active-users-pagination .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
    }

    #active-users-pagination .page-link:hover {
        color: #764ba2;
        background-color: #f8f9fa;
    }

    /* Loading State */
    #active-users-loading {
        display: inline-block;
    }

     /* Filter Section */
    #active-users #active-users-search,
    #active-users #active-users-role-filter,
    #active-users #active-users-team-filter {
        font-size: 0.875rem;
        background-color: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #212529 !important;
        font-weight: 500;
    }

    #active-users #active-users-search::placeholder {
        color: #6c757d !important;
        opacity: 1;
    }

    #active-users #active-users-search:focus,
    #active-users #active-users-role-filter:focus,
    #active-users #active-users-team-filter:focus {
        background-color: #ffffff !important;
        border-color: #ffffff !important;
        color: #212529 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.3) !important;
    }

    #active-users .input-group-text {
        background-color: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-right: none !important;
        color: #495057 !important;
    }

    #active-users #active-users-search {
        border-left: none !important;
    }

    #active-users #active-users-search:focus {
        border-left: 1px solid #ffffff !important;
    }

    #active-users #active-users-clear-filters {
        background-color: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #212529 !important;
        font-weight: 600;
    }

    #active-users #active-users-clear-filters:hover {
        background-color: #ffffff !important;
        border-color: #ffffff !important;
        color: #212529 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #active-users #active-users-refresh {
        background-color: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #212529 !important;
        font-weight: 600;
    }

    #active-users #active-users-refresh:hover {
        background-color: #ffffff !important;
        border-color: #ffffff !important;
        color: #212529 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

     /* Card Header Improvements - General */
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    /* Active Users Badge on Blue Background */
    #active-users .active-users-badge.badge-success {
        background-color: #28a745 !important;
        color: #ffffff !important;
        border: 2px solid rgba(255, 255, 255, 0.9);
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    #active-users .active-users-badge.badge-secondary {
        background-color: rgba(255, 255, 255, 0.95) !important;
        color: #495057 !important;
        border: 2px solid rgba(255, 255, 255, 0.9);
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    #active-users .active-users-badge.badge-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
        border: 2px solid rgba(255, 255, 255, 0.9);
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    /* Table Improvements */
    #active-users-table thead th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    #active-users-table tbody td {
        vertical-align: middle;
    }

    /* Typography Improvements */
    #active-users-table strong {
        font-weight: 600;
        color: #212529;
    }

    #active-users-table .text-muted {
        color: #6c757d !important;
        font-size: 0.8125rem;
    }

    /* Empty State */
    #active-users-empty-message {
        color: #6c757d;
        font-size: 0.9375rem;
    }

    /* Card Footer */
    #active-users .card-footer {
        background-color: #ffffff;
        border-top: 1px solid #dee2e6;
        color: #495057;
    }

    #active-users-last-refresh {
        color: #6c757d;
        font-weight: 500;
    }
</style>
@endpush

