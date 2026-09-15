<?php

namespace Database\Factories;

use App\Models\StaffMatterSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffMatterSession>
 */
class StaffMatterSessionFactory extends Factory
{
    protected $model = StaffMatterSession::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = now();

        return [
            'staff_id' => 1,
            'client_matter_id' => null,
            'client_id' => 1,
            'matter_key' => 0,
            'session_date' => $now->toDateString(),
            'status' => StaffMatterSession::STATUS_ACCESSED,
            'focused_seconds' => 0,
            'idle_cut_seconds' => 0,
            'confirmed_minutes' => null,
            'event_count' => null,
            'is_reviewed_only' => false,
            'started_at' => $now,
            'last_heartbeat_at' => $now,
            'ended_at' => null,
            'activities_log_id' => null,
        ];
    }

    public function accessed(): static
    {
        return $this->state(fn (): array => [
            'status' => StaffMatterSession::STATUS_ACCESSED,
        ]);
    }

    public function recorded(): static
    {
        return $this->state(fn (): array => [
            'status' => StaffMatterSession::STATUS_RECORDED,
            'focused_seconds' => 300,
            'confirmed_minutes' => 5,
            'event_count' => 2,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (): array => [
            'status' => StaffMatterSession::STATUS_CLOSED,
            'focused_seconds' => 300,
            'confirmed_minutes' => 5,
            'event_count' => 2,
            'ended_at' => now(),
        ]);
    }

    public function reviewedOnly(): static
    {
        return $this->state(fn (): array => [
            'status' => StaffMatterSession::STATUS_RECORDED,
            'focused_seconds' => 150,
            'is_reviewed_only' => true,
            'event_count' => 1,
        ]);
    }
}
