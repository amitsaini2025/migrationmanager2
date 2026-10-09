<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\ClientMatter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

/**
 * Writes Labour Market Testing fields onto one active company matter.
 * The company page and the LMT sheet both use this so the two screens stay in step.
 */
final class LmtMatterWriter
{
    /**
     * @param  array<string, mixed>  $input
     * @return array{ok: bool, status: int, message: string}
     */
    public function save(Admin $client, array $input): array
    {
        if (! $client->is_company) {
            return $this->fail(400, 'Not a company client');
        }

        $matterId = $input['client_matter_id'] ?? null;
        if ($matterId === null || $matterId === '') {
            return $this->fail(422, 'Matter is required to save Labour Market Testing details.');
        }

        $matter = ClientMatter::query()
            ->where('id', (int) $matterId)
            ->where('client_id', $client->id)
            ->first();

        if (! $matter) {
            return $this->fail(404, 'Matter not found.');
        }

        if ((int) $matter->matter_status !== 1) {
            return $this->fail(422, 'LMT can only be updated for active matters.');
        }

        if (filter_var($input['delete_lmt'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $matter->lmt_required = null;
            $matter->lmt_start_date = null;
            $matter->lmt_end_date = null;
            $matter->lmt_notes = null;
            $matter->lmt_password = null;
            $matter->save();

            return $this->ok('Labour Market Testing details removed for this matter. You can add a new record anytime.');
        }

        $input['lmt_start_date'] = $this->blankToNull($input['lmt_start_date'] ?? null);
        $input['lmt_end_date'] = $this->blankToNull($input['lmt_end_date'] ?? null);

        $validator = Validator::make($input, [
            'lmt_start_date' => ['nullable', 'date'],
            'lmt_end_date' => ['nullable', 'date'],
            'lmt_notes' => ['nullable', 'string'],
            'lmt_password' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->fail(422, (string) $validator->errors()->first());
        }

        $validated = $validator->validated();
        $lmtRequired = $this->requiredFlag($input['lmt_required'] ?? null);
        $start = $validated['lmt_start_date'] ?? null;
        $end = $validated['lmt_end_date'] ?? null;

        if ($start !== null && $end !== null) {
            try {
                if (Carbon::parse($end)->lt(Carbon::parse($start))) {
                    return $this->fail(422, 'LMT end date must be on or after the start date.');
                }
            } catch (\Throwable) {
                return $this->fail(422, 'Invalid LMT date values.');
            }
        }

        $notes = isset($validated['lmt_notes']) ? trim((string) $validated['lmt_notes']) : '';
        $password = isset($validated['lmt_password']) ? trim((string) $validated['lmt_password']) : '';

        $matter->lmt_required = $lmtRequired;
        $matter->lmt_start_date = $start;
        $matter->lmt_end_date = $end;
        $matter->lmt_notes = $notes !== '' ? $notes : null;
        $matter->lmt_password = $password !== '' ? $password : null;
        $matter->save();

        return $this->ok('LMT details updated successfully');
    }

    /**
     * @return array{ok: bool, status: int, message: string}
     */
    private function ok(string $message): array
    {
        return ['ok' => true, 'status' => 200, 'message' => $message];
    }

    /**
     * @return array{ok: bool, status: int, message: string}
     */
    private function fail(int $status, string $message): array
    {
        return ['ok' => false, 'status' => $status, 'message' => $message];
    }

    private function blankToNull(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return trim((string) $value) === '' ? null : $value;
    }

    private function requiredFlag(mixed $required): ?bool
    {
        if ($required === '1' || $required === 1 || $required === true) {
            return true;
        }

        if ($required === '0' || $required === 0 || $required === false) {
            return false;
        }

        return null;
    }
}
