<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kyslik\ColumnSortable\Sortable;

class Document extends Model
{
    use Sortable;

    protected $table = 'documents';

    protected $fillable = [
        'file_name',
        'filetype', 
        'myfile',
        'myfile_key',
        'user_id',
        'client_id',
        'file_size',
        'type',
        'doc_type',
        'folder_name',
        'mail_type',
        'client_matter_id',
        'checklist',
        'checklist_verified_by',
        'checklist_verified_at',
        'not_used_doc',
        'status',
        'signature_doc_link',
        'signed_doc_link',
        'is_client_portal_verify',
        'client_portal_verified_by',
        'client_portal_verified_at',
        'created_by',
        'origin',
        'documentable_type',
        'documentable_id',
        'title',
        'document_type',
        'labels',
        'due_at',
        'priority',
        'primary_signer_email',
        'signer_count',
        'last_activity_at',
        'archived_at'
    ];

    protected $casts = [
        'labels' => 'array',
        'checklist_verified_at' => 'datetime',
        'client_portal_verified_at' => 'datetime',
        'due_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = [
        'id',
        'file_name',
        'status',
        'created_at',
        'updated_at',
        'title',
        'document_type',
        'priority'
    ];

    // Relationships
    public function signers(): HasMany
    {
        return $this->hasMany(Signer::class);
    }

    public function signatureFields(): HasMany
    {
        return $this->hasMany(SignatureField::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssociated($query)
    {
        return $query->whereNotNull('documentable_type')
                    ->whereNotNull('documentable_id');
    }

    public function scopeAdhoc($query)
    {
        return $query->whereNull('documentable_type')
                    ->whereNull('documentable_id');
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    // Accessors
    public function getDisplayTitleAttribute()
    {
        return $this->title ?: $this->file_name;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'sent' => 'warning', 
            'signed' => 'success',
            default => 'secondary'
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_at && $this->due_at->isPast() && $this->status !== 'signed';
    }
}
