@extends('layouts.admin_client_detail_dashboard')

@section('content')
<style>
    .followup-filters {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }
    
    .followup-table {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .followup-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .followup-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    .followup-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .followup-table tr:hover {
        background: #f8f9fa;
    }
    
    .status-badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-pending { background: #ffa426; color: white; }
    .status-completed { background: #47c363; color: white; }
    .status-overdue { background: #fc544b; color: white; }
    .status-cancelled { background: #95aac9; color: white; }
    .status-rescheduled { background: #6777ef; color: white; }
</style>

<div class="followup-dashboard" style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div class="client-header" style="padding-bottom: 20px;">
        <div>
            <h1><i class="fas fa-list"></i> All Follow-ups</h1>
        </div>
        <div class="client-status">
            <a href="{{ route('admin.leads.followups.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-dashboard"></i> Dashboard
            </a>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="followup-filters">
        <form method="GET" action="{{ route('admin.leads.followups.index') }}">
            <div class="filter-row">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="call" {{ request('type') == 'call' ? 'selected' : '' }}>Call</option>
                        <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="meeting" {{ request('type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="sms" {{ request('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="whatsapp" {{ request('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label>Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Table -->
    <div class="followup-table">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Type</th>
                        <th>Scheduled Date</th>
                        <th>Assigned To</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followups as $followup)
                        <tr>
                            <td>
                                <a href="{{ route('admin.lead.edit', $followup->lead_id) }}">
                                    {{ $followup->lead->first_name }} {{ $followup->lead->last_name }}
                                </a>
                                <div style="font-size: 12px; color: #6c757d;">
                                    {{ $followup->lead->email }}
                                </div>
                            </td>
                            <td>
                                <span class="type-badge">
                                    <i class="fas fa-{{ $followup->followup_type == 'call' ? 'phone' : ($followup->followup_type == 'email' ? 'envelope' : ($followup->followup_type == 'meeting' ? 'users' : 'comment')) }}"></i>
                                    {{ ucfirst($followup->followup_type) }}
                                </span>
                            </td>
                            <td>{{ $followup->scheduled_date->format('d M Y, h:i A') }}</td>
                            <td>{{ $followup->assignedAgent->first_name ?? 'Unassigned' }}</td>
                            <td>
                                <span class="priority-badge priority-{{ $followup->priority }}">
                                    {{ ucfirst($followup->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $followup->status }}">
                                    {{ ucfirst($followup->status) }}
                                </span>
                            </td>
                            <td>
                                @if($followup->status == 'pending')
                                    <button class="btn btn-sm btn-success" onclick="completeFollowup({{ $followup->id }})" title="Complete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="rescheduleFollowup({{ $followup->id }})" title="Reschedule">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-info" onclick="viewDetails({{ $followup->id }})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #95aac9;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i>
                                <p>No follow-ups found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div style="margin-top: 20px;">
            {{ $followups->links() }}
        </div>
    </div>
</div>

<!-- Include modals from dashboard -->
@include('Admin.leads.followups.dashboard')

@endsection

