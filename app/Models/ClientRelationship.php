<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClientRelationship extends Model
{
    protected $table = 'client_relationships';

    /**
     * Partner/spouse relationship types shown in the Partner family section.
     * Keep in sync with clients/leads Partner edit UI and partner_relationship_type validation.
     *
     * @var list<string>
     */
    public const PARTNER_RELATIONSHIP_TYPES = [
        'Husband',
        'Wife',
        'Ex-Husband',
        'Ex-Wife',
        'Defacto',
        'Engaged',
    ];

    protected $fillable = [
        'admin_id',
        'client_id',
        'related_client_id',
        'details',
        'relationship_type',
        'company_type',
        'email',
        'first_name',
        'last_name',
        'phone',
        'gender',
        'dob',
    ];

    /**
     * Limit query to Partner section relationship types (not children/parents/etc).
     */
    public function scopePartners(Builder $query): Builder
    {
        return $query->whereIn('relationship_type', self::PARTNER_RELATIONSHIP_TYPES);
    }

    /**
     * Get the related client (partner/child/etc)
     * Enables eager loading to prevent N+1 queries
     */
    public function relatedClient()
    {
        return $this->belongsTo(Admin::class, 'related_client_id', 'id');
    }

    /**
     * Get the main client
     */
    public function client()
    {
        return $this->belongsTo(Admin::class, 'client_id', 'id');
    }
}
