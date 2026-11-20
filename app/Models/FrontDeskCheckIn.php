<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class FrontDeskCheckIn extends Model
{
    protected $table = 'front_desk_check_ins';

    protected $fillable = [
        'admin_id',
        'phone_normalized',
        'email',
        'client_id',
        'lead_id',
        'appointment_id',
        'claimed_appointment',
        'visit_reason',
        'visit_notes',
        'notified_staff_id',
        'notified_at',
        'metadata',
    ];

    protected $casts = [
        'claimed_appointment' => 'boolean',
        'notified_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'admin_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'lead_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(BookingAppointment::class, 'appointment_id');
    }

    public function notifiedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'notified_staff_id');
    }

    /**
     * The CRM record (client or lead) as a unified accessor.
     */
    public function getCrmRecordAttribute(): ?Admin
    {
        if ($this->client_id) {
            return Admin::find($this->client_id);
        }
        if ($this->lead_id) {
            return Admin::find($this->lead_id);
        }

        return null;
    }

    /**
     * Check-in log ids created by the public page (not the staff wizard).
     *
     * @param  list<int>  $checkinLogIds
     * @return list<int>
     */
    public static function publicCheckInLogIds(array $checkinLogIds): array
    {
        $wanted = [];
        foreach ($checkinLogIds as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $wanted[$id] = true;
            }
        }

        if ($wanted === [] || ! Schema::hasTable('front_desk_check_ins')) {
            return [];
        }

        return static::query()
            ->where('metadata->source', 'public')
            ->get(['metadata'])
            ->map(fn (self $row): int => (int) data_get($row->metadata, 'checkin_log_id'))
            ->filter(fn (int $id): bool => $id > 0 && isset($wanted[$id]))
            ->unique()
            ->values()
            ->all();
    }

    public static function isPublicCheckInLog(int $checkinLogId): bool
    {
        return in_array($checkinLogId, self::publicCheckInLogIds([$checkinLogId]), true);
    }

    public static function visitReasons(): array
    {
        return [
            'general_enquiry' => 'General Enquiry',
            'appointment_followup' => 'Appointment Follow-up',
            'document_submission' => 'Document Submission',
            'payment' => 'Payment',
            'other' => 'Other',
        ];
    }
}
