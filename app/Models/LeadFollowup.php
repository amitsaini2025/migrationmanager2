<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFollowup extends Model
{
    protected $fillable = [
        'lead_id',
        'assigned_to',
        'created_by',
        'followup_type',
        'priority',
        'scheduled_date',
        'completed_at',
        'status',
        'outcome',
        'next_followup_date',
        'notes',
        'reminder_sent',
        'reminder_sent_at',
    ];
    
    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_at' => 'datetime',
        'next_followup_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'reminder_sent' => 'boolean',
    ];
    
    // Relationships
    public function lead()
    {
        return $this->belongsTo(Admin::class, 'lead_id');
    }
    
    public function assignedAgent()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }
    
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    
    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('scheduled_date', '<', now());
    }
    
    public function scopeDueToday($query)
    {
        return $query->where('status', 'pending')
                    ->whereDate('scheduled_date', today());
    }
    
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('assigned_to', $agentId);
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'pending')
                    ->where('scheduled_date', '>', now())
                    ->orderBy('scheduled_date');
    }
}
