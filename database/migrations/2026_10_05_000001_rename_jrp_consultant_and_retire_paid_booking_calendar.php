<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename JRP consultant display name, retire Employer Sponsored booking calendar UI,
     * and move future active paid-calendar appointments to JRP (free) or Ajay (paid).
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'jrp')
            ->where('location', 'melbourne')
            ->update([
                'name' => 'Shubham (JRP)',
                'updated_at' => now(),
            ]);

        $paidConsultantIds = DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->pluck('id');

        if ($paidConsultantIds->isNotEmpty() && Schema::hasTable('booking_appointments')) {
            $jrpConsultantId = DB::table('appointment_consultants')
                ->where('calendar_type', 'jrp')
                ->where('location', 'melbourne')
                ->orderByDesc('is_active')
                ->value('id');

            $ajayConsultantId = DB::table('appointment_consultants')
                ->where('calendar_type', 'ajay')
                ->where('location', 'melbourne')
                ->where('is_active', true)
                ->value('id');

            $now = now();

            $appointments = DB::table('booking_appointments')
                ->whereIn('consultant_id', $paidConsultantIds)
                ->where('appointment_datetime', '>', $now)
                ->whereNotIn('status', ['cancelled', 'no_show', 'completed'])
                ->get(['id', 'consultant_id', 'is_paid', 'service_id']);

            foreach ($appointments as $appointment) {
                $bucket = $this->resolveFreeVsPaidBucket($appointment);
                $targetConsultantId = match ($bucket) {
                    'paid' => $ajayConsultantId,
                    'free' => $jrpConsultantId,
                    default => $jrpConsultantId,
                };

                if ($targetConsultantId === null || (int) $appointment->consultant_id === (int) $targetConsultantId) {
                    continue;
                }

                DB::table('booking_appointments')
                    ->where('id', $appointment->id)
                    ->update([
                        'consultant_id' => $targetConsultantId,
                        'updated_at' => now(),
                    ]);
            }
        }

        $paidRetire = [
            'is_active' => false,
            'updated_at' => now(),
        ];
        if (Schema::hasColumn('appointment_consultants', 'show_in_filter')) {
            $paidRetire['show_in_filter'] = false;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->update($paidRetire);
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        $paidRestore = [
            'is_active' => true,
            'updated_at' => now(),
        ];
        if (Schema::hasColumn('appointment_consultants', 'show_in_filter')) {
            $paidRestore['show_in_filter'] = true;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->update($paidRestore);

        DB::table('appointment_consultants')
            ->where('calendar_type', 'jrp')
            ->where('location', 'melbourne')
            ->where('name', 'Shubham (JRP)')
            ->update([
                'name' => 'Shubham/Yadwinder (JRP)',
                'updated_at' => now(),
            ]);
    }

    /**
     * @return 'free'|'paid'|null
     */
    protected function resolveFreeVsPaidBucket(object $appointment): ?string
    {
        if ($appointment->is_paid === false || $appointment->is_paid === 0 || $appointment->is_paid === '0') {
            return 'free';
        }
        if ($appointment->is_paid === true || $appointment->is_paid === 1 || $appointment->is_paid === '1') {
            return 'paid';
        }

        $serviceId = $appointment->service_id;
        if ($serviceId !== null && $serviceId !== '') {
            $sid = (int) $serviceId;
            if ($sid === 2) {
                return 'free';
            }
            if (in_array($sid, [1, 3], true)) {
                return 'paid';
            }
        }

        return null;
    }
};
