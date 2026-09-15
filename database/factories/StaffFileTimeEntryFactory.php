<?php

namespace Database\Factories;

use App\Models\StaffFileTimeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffFileTimeEntry>
 */
class StaffFileTimeEntryFactory extends Factory
{
    protected $model = StaffFileTimeEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => 1,
            'client_matter_id' => null,
            'client_id' => null,
            'kind' => StaffFileTimeEntry::KIND_DRAFT,
            'title' => fake()->sentence(3),
            'status' => StaffFileTimeEntry::STATUS_DOING,
            'is_running' => true,
            'clock_seconds' => 0,
            'confirmed_minutes' => null,
            'started_at' => now(),
            'completed_at' => null,
            'activities_log_id' => null,
        ];
    }

    public function parked(): static
    {
        return $this->state(fn (): array => [
            'status' => StaffFileTimeEntry::STATUS_PARKED,
            'is_running' => false,
            'clock_seconds' => 120,
        ]);
    }

    public function done(int $minutes = 10): static
    {
        return $this->state(fn (): array => [
            'status' => StaffFileTimeEntry::STATUS_DONE,
            'is_running' => false,
            'clock_seconds' => $minutes * 60,
            'confirmed_minutes' => $minutes,
            'completed_at' => now(),
        ]);
    }

    public function forMatter(int $matterId, int $clientId): static
    {
        return $this->state(fn (): array => [
            'client_matter_id' => $matterId,
            'client_id' => $clientId,
        ]);
    }
}
