<?php

namespace App\Models;

use Database\Factories\StaffDaySummaryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffDaySummary extends Model
{
    /** @use HasFactory<StaffDaySummaryFactory> */
    use HasFactory;

    public const SOURCE_COPY = 'copy';

    public const SOURCE_SCHEDULE = 'schedule';

    public const SOURCE_BACKFILL = 'backfill';

    protected $fillable = [
        'staff_id',
        'summary_date',
        'body',
        'source',
        'saved_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'source' => self::SOURCE_COPY,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'summary_date' => 'date',
            'saved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
