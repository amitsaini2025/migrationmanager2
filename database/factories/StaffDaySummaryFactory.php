<?php

namespace Database\Factories;

use App\Models\StaffDaySummary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffDaySummary>
 */
class StaffDaySummaryFactory extends Factory
{
    protected $model = StaffDaySummary::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => 1,
            'summary_date' => now()->toDateString(),
            'body' => 'Staff — '.now()->format('l, j M Y')."\nHours in CRM: —\n\n— Already in CRM —\n(none)",
            'source' => StaffDaySummary::SOURCE_COPY,
            'saved_at' => now(),
        ];
    }
}
