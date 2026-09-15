<?php

namespace App\Models;

use Database\Factories\StaffMatterSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffMatterSession extends Model
{
    /** @use HasFactory<StaffMatterSessionFactory> */
    use HasFactory;

    public const STATUS_ACCESSED = 'accessed';

    public const STATUS_RECORDED = 'recorded';

    public const STATUS_CLOSED = 'closed';

    public const ACTIVITY_TYPE = 'file_time';

    protected $fillable = [
        'staff_id',
        'client_matter_id',
        'client_id',
        'matter_key',
        'session_date',
        'status',
        'focused_seconds',
        'idle_cut_seconds',
        'confirmed_minutes',
        'event_count',
        'is_reviewed_only',
        'started_at',
        'last_heartbeat_at',
        'ended_at',
        'activities_log_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'is_reviewed_only' => 'boolean',
            'started_at' => 'datetime',
            'last_heartbeat_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StaffMatterSession $session): void {
            $session->matter_key = (int) ($session->client_matter_id ?? 0);
        });
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * @return BelongsTo<ClientMatter, $this>
     */
    public function clientMatter(): BelongsTo
    {
        return $this->belongsTo(ClientMatter::class, 'client_matter_id');
    }

    /**
     * @return BelongsTo<Admin, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * @return BelongsTo<ActivitiesLog, $this>
     */
    public function activitiesLog(): BelongsTo
    {
        return $this->belongsTo(ActivitiesLog::class, 'activities_log_id');
    }

    public function isRecordedForBoard(): bool
    {
        if ($this->status === self::STATUS_RECORDED) {
            return true;
        }

        if ($this->status !== self::STATUS_CLOSED) {
            return false;
        }

        return (bool) $this->is_reviewed_only
            || (int) ($this->event_count ?? 0) > 0
            || $this->activities_log_id !== null;
    }
}
