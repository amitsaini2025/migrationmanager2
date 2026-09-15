<?php

namespace App\Models;

use Database\Factories\StaffFileTimeEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffFileTimeEntry extends Model
{
    /** @use HasFactory<StaffFileTimeEntryFactory> */
    use HasFactory;

    public const KIND_DRAFT = 'draft';

    public const KIND_IMMI = 'immi';

    public const KIND_DOCS = 'docs';

    public const KIND_MAILBOX = 'mailbox';

    public const KIND_INTERNAL = 'internal';

    public const KIND_OTHER = 'other';

    public const STATUS_DOING = 'doing';

    public const STATUS_PARKED = 'parked';

    public const STATUS_DONE = 'done';

    public const ACTIVITY_TYPE = 'file_time';

    /**
     * @return list<string>
     */
    public static function kinds(): array
    {
        return [
            self::KIND_DRAFT,
            self::KIND_IMMI,
            self::KIND_DOCS,
            self::KIND_MAILBOX,
            self::KIND_INTERNAL,
            self::KIND_OTHER,
        ];
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DOING,
            self::STATUS_PARKED,
            self::STATUS_DONE,
        ];
    }

    protected $fillable = [
        'staff_id',
        'client_matter_id',
        'client_id',
        'kind',
        'title',
        'status',
        'is_running',
        'clock_seconds',
        'confirmed_minutes',
        'started_at',
        'completed_at',
        'activities_log_id',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_DOING,
        'is_running' => false,
        'clock_seconds' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_running' => 'boolean',
            'clock_seconds' => 'integer',
            'confirmed_minutes' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function clientMatter(): BelongsTo
    {
        return $this->belongsTo(ClientMatter::class, 'client_matter_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    public function activitiesLog(): BelongsTo
    {
        return $this->belongsTo(ActivitiesLog::class, 'activities_log_id');
    }

    public function isAdmin(): bool
    {
        return $this->client_matter_id === null;
    }

    public function isPosted(): bool
    {
        return $this->activities_log_id !== null;
    }

    public function kindLabel(): string
    {
        return match ($this->kind) {
            self::KIND_DRAFT => 'drafting',
            self::KIND_IMMI => 'Immi/portal',
            self::KIND_DOCS => 'doc check',
            self::KIND_MAILBOX => 'mailbox',
            self::KIND_INTERNAL => 'internal',
            self::KIND_OTHER => 'other',
            default => (string) $this->kind,
        };
    }
}
