<?php

namespace App\Models;

use App\Support\LeadMigrateInviteChannels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadMigrateInvite extends Model
{
    protected $fillable = [
        'token_hash',
        'channel',
        'sent_to_email',
        'sent_to_phone',
        'sent_to_phone_e164',
        'sent_by',
        'lead_id',
        'source',
        'submitted_payload',
        'expires_at',
        'used_at',
        'invalidated_at',
        'submitted_at',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [
        'token_hash',
    ];

    protected $casts = [
        'submitted_payload' => 'array',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'invalidated_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'sent_by');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'lead_id');
    }

    public function isUsable(): bool
    {
        if ($this->used_at !== null || $this->invalidated_at !== null) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeUsable(Builder $query): Builder
    {
        return $query
            ->whereNull('used_at')
            ->whereNull('invalidated_at')
            ->where(function (Builder $q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function isEmailChannel(): bool
    {
        return $this->channel === LeadMigrateInviteChannels::CHANNEL_EMAIL;
    }

    public function isSmsChannel(): bool
    {
        return $this->channel === LeadMigrateInviteChannels::CHANNEL_SMS;
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }
}
