{{-- Followup Card Component --}}
<div class="followup-card {{ $status ?? '' }}">
    <div class="followup-header">
        <div class="lead-info">
            <div class="lead-name">
                <a href="{{ route('admin.lead.edit', $followup->lead_id) }}" style="color: inherit; text-decoration: none;">
                    {{ $followup->lead->first_name }} {{ $followup->lead->last_name }}
                </a>
            </div>
            <div class="followup-meta">
                <span>
                    <i class="fas fa-calendar"></i>
                    {{ $followup->scheduled_date->format('d M Y, h:i A') }}
                </span>
                <span>
                    <i class="fas fa-user"></i>
                    {{ $followup->assignedAgent->first_name ?? 'Unassigned' }}
                </span>
                <span class="type-badge">
                    <i class="fas fa-{{ $followup->followup_type == 'call' ? 'phone' : ($followup->followup_type == 'email' ? 'envelope' : ($followup->followup_type == 'meeting' ? 'users' : 'comment')) }}"></i>
                    {{ ucfirst($followup->followup_type) }}
                </span>
                <span class="priority-badge priority-{{ $followup->priority }}">
                    {{ ucfirst($followup->priority) }}
                </span>
            </div>
            @if($followup->notes)
                <div style="margin-top: 10px; font-size: 13px; color: #6c757d;">
                    {{ Str::limit($followup->notes, 100) }}
                </div>
            @endif
        </div>
        <div class="followup-actions">
            <button class="btn-action btn-complete" onclick="completeFollowup({{ $followup->id }})" title="Complete">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn-action btn-reschedule" onclick="rescheduleFollowup({{ $followup->id }})" title="Reschedule">
                <i class="fas fa-clock"></i>
            </button>
        </div>
    </div>
</div>

