<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class CompanyNomination extends Model
{
    protected $table = 'company_nominations';

    protected $fillable = [
        'company_id',
        'position_title',
        'anzsco_code',
        'position_description',
        'salary',
        'duration',
        'nominated_client_id',
        'nominated_person_name',
        'client_matter_id',
        'trn',
        'status',
        'nomination_date',
        'expiry_date',
        'sort_order',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'nomination_date' => 'date',
        'expiry_date' => 'date',
        'sort_order' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Nominated person when they exist in system (client/lead).
     * Search scope: type in ['client','lead'].
     */
    public function nominatedClient(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'nominated_client_id', 'id');
    }

    /**
     * Primary/legacy single matter link (kept for dual-write compatibility).
     */
    public function clientMatter(): BelongsTo
    {
        return $this->belongsTo(ClientMatter::class, 'client_matter_id');
    }

    /**
     * All linked client matters for this nomination.
     */
    public function clientMatters(): BelongsToMany
    {
        return $this->belongsToMany(
            ClientMatter::class,
            'company_nomination_client_matter',
            'company_nomination_id',
            'client_matter_id'
        )->withTimestamps();
    }

    /**
     * Sync pivot matters and dual-write the first ID into client_matter_id.
     *
     * @param  array<int, int|string|null>  $clientMatterIds
     */
    public function syncClientMatters(array $clientMatterIds): void
    {
        $ids = self::normalizeClientMatterIds($clientMatterIds);

        $this->clientMatters()->sync($ids);

        $primaryId = $ids[0] ?? null;
        if ((int) ($this->client_matter_id ?? 0) !== (int) ($primaryId ?? 0)) {
            $this->forceFill(['client_matter_id' => $primaryId])->save();
        }
    }

    /**
     * Matter labels for summary/detail display (pivot first, then legacy FK).
     *
     * @return Collection<int, string>
     */
    public function clientMatterLabels(): Collection
    {
        if ($this->relationLoaded('clientMatters') && $this->clientMatters->isNotEmpty()) {
            return $this->clientMatters
                ->map(fn (ClientMatter $matter) => $matter->dropdownLabel())
                ->filter()
                ->values();
        }

        if ($this->relationLoaded('clientMatter') && $this->clientMatter) {
            $label = $this->clientMatter->dropdownLabel();

            return filled($label) ? collect([(string) $label]) : collect();
        }

        return collect();
    }

    /**
     * @param  array<int, int|string|null>|int|string|null  $clientMatterIds
     * @return list<int>
     */
    public static function normalizeClientMatterIds(array|int|string|null $clientMatterIds): array
    {
        if (! is_array($clientMatterIds)) {
            $clientMatterIds = ($clientMatterIds === null || $clientMatterIds === '')
                ? []
                : [$clientMatterIds];
        }

        return collect($clientMatterIds)
            ->flatten()
            ->map(fn ($id) => is_numeric($id) ? (int) $id : null)
            ->filter(fn ($id) => $id !== null && $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
