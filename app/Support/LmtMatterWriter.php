<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\ClientMatter;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

/**
 * Writes Labour Market Testing fields onto one active company matter.
 * The company page and the LMT sheet both use this so the two screens stay in step.
 */
final class LmtMatterWriter
{
    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $files
     * @return array{ok: bool, status: int, message: string, uploaded: bool}
     */
    public function save(Admin $client, array $input, array $files = [], ?int $userId = null): array
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
            $this->clear($matter);

            return $this->ok('Labour Market Testing details removed for this matter. You can add a new record anytime.');
        }

        $slots = [
            1 => $this->slotFromInput($input, 1),
            2 => $this->slotFromInput($input, 2),
        ];
        $manageAdvertisements = $this->requestIncludesAdvertisements($input);
        $uploads = $this->uploadsFrom($files);
        $fileErrors = (new LmtAdvertisementFiles)->validateFiles(array_values($uploads));
        if ($fileErrors !== []) {
            return $this->fail(422, $fileErrors[0]);
        }
        if ($uploads !== [] && trim((string) ($client->client_id ?? '')) === '') {
            return $this->fail(422, 'This company has no client reference, so advertisement files cannot be stored.');
        }

        foreach ($slots as $number => $slot) {
            if ($slot['closed_on'] !== null && $slot['opened_on'] === null) {
                return $this->fail(422, 'Advertisement '.$number.' needs the date applications opened.');
            }
            if ($slot['opened_on'] !== null && $slot['closed_on'] !== null) {
                try {
                    if (Carbon::parse($slot['closed_on'])->lt(Carbon::parse($slot['opened_on']))) {
                        return $this->fail(422, 'Advertisement '.$number.' close date must be on or after the date applications opened.');
                    }
                } catch (\Throwable) {
                    return $this->fail(422, 'Invalid advertisement date values.');
                }
            }
        }

        $input['lmt_start_date'] = $this->blankToNull($input['lmt_start_date'] ?? null);
        $input['lmt_end_date'] = $this->blankToNull($input['lmt_end_date'] ?? null);

        $validator = Validator::make($input, [
            'lmt_start_date' => ['nullable', 'date'],
            'lmt_end_date' => ['nullable', 'date'],
            'lmt_notes' => ['nullable', 'string'],
            'lmt_password' => ['nullable', 'string', 'max:255'],
            'lmt_ad1_publication' => ['nullable', 'string', 'max:255'],
            'lmt_ad1_opened_on' => ['nullable', 'date'],
            'lmt_ad1_closed_on' => ['nullable', 'date'],
            'lmt_ad2_publication' => ['nullable', 'string', 'max:255'],
            'lmt_ad2_opened_on' => ['nullable', 'date'],
            'lmt_ad2_closed_on' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return $this->fail(422, (string) $validator->errors()->first());
        }

        $bothPresent = $manageAdvertisements && $this->slotIsPresent($slots[1]) && $this->slotIsPresent($slots[2]);
        $useAdvertisements = $bothPresent || (bool) $matter->lmt_use_advertisements;

        if ($useAdvertisements && $manageAdvertisements) {
            $present = array_values(array_filter($slots, fn (array $slot) => $this->slotIsPresent($slot)));
            [$start, $end] = $this->datesFromSlots($present);
        } elseif ($useAdvertisements) {
            $start = $matter->lmt_start_date?->format('Y-m-d');
            $end = $matter->lmt_end_date?->format('Y-m-d');
        } else {
            $validated = $validator->validated();
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
        }

        $validated = $validator->validated();
        $notes = isset($validated['lmt_notes']) ? trim((string) $validated['lmt_notes']) : '';
        $password = isset($validated['lmt_password']) ? trim((string) $validated['lmt_password']) : '';

        $matter->lmt_required = $this->requiredFlag($input['lmt_required'] ?? null);
        $matter->lmt_start_date = $start;
        $matter->lmt_end_date = $end;
        $matter->lmt_notes = $notes !== '' ? $notes : null;
        $matter->lmt_password = $password !== '' ? $password : null;
        if ($manageAdvertisements) {
            $matter->lmt_use_advertisements = $useAdvertisements ? true : $matter->lmt_use_advertisements;
            $this->assignSlot($matter, 1, $slots[1]);
            $this->assignSlot($matter, 2, $slots[2]);
        }
        $matter->save();

        $uploaded = false;
        if ($uploads !== []) {
            try {
                $stored = new LmtAdvertisementFiles;
                foreach ($uploads as $key => $file) {
                    $number = $key === 'ad2' ? 2 : 1;
                    $document = $stored->storeOne($client, $matter, $file, (int) $userId, $number);
                    $matter->{'lmt_ad'.$number.'_document_id'} = $document->id;
                    $uploaded = true;
                }
                $matter->save();
            } catch (\Throwable $e) {
                report($e);

                return $this->fail(500, 'LMT details were saved, but the advertisement file could not be uploaded.');
            }
        }

        return $this->ok('LMT details updated successfully', $uploaded);
    }

    private function clear(ClientMatter $matter): void
    {
        $matter->lmt_required = null;
        $matter->lmt_start_date = null;
        $matter->lmt_end_date = null;
        $matter->lmt_notes = null;
        $matter->lmt_password = null;
        $matter->lmt_use_advertisements = null;
        $matter->lmt_ad1_publication = null;
        $matter->lmt_ad1_opened_on = null;
        $matter->lmt_ad1_closed_on = null;
        $matter->lmt_ad1_document_id = null;
        $matter->lmt_ad2_publication = null;
        $matter->lmt_ad2_opened_on = null;
        $matter->lmt_ad2_closed_on = null;
        $matter->lmt_ad2_document_id = null;
        $matter->save();
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function requestIncludesAdvertisements(array $input): bool
    {
        foreach ([1, 2] as $number) {
            foreach (['publication', 'opened_on', 'closed_on'] as $field) {
                if (array_key_exists('lmt_ad'.$number.'_'.$field, $input)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{publication: string, opened_on: ?string, closed_on: ?string}
     */
    private function slotFromInput(array $input, int $number): array
    {
        return [
            'publication' => trim((string) ($input['lmt_ad'.$number.'_publication'] ?? '')),
            'opened_on' => $this->blankToNull($input['lmt_ad'.$number.'_opened_on'] ?? null),
            'closed_on' => $this->blankToNull($input['lmt_ad'.$number.'_closed_on'] ?? null),
        ];
    }

    /**
     * @param  array{publication: string, opened_on: ?string, closed_on: ?string}  $slot
     */
    private function slotIsPresent(array $slot): bool
    {
        return $slot['publication'] !== '' && $slot['opened_on'] !== null;
    }

    /**
     * @param  list<array{publication: string, opened_on: ?string, closed_on: ?string}>  $slots
     * @return array{0: ?string, 1: ?string}
     */
    private function datesFromSlots(array $slots): array
    {
        if ($slots === []) {
            return [null, null];
        }

        $opens = array_map(fn (array $slot) => (string) $slot['opened_on'], $slots);
        sort($opens);
        $start = $opens[0];
        foreach ($slots as $slot) {
            if ($slot['closed_on'] === null) {
                return [$start, null];
            }
        }

        $closes = array_map(fn (array $slot) => (string) $slot['closed_on'], $slots);
        sort($closes);

        return [$start, $closes[count($closes) - 1]];
    }

    /**
     * @param  array{publication: string, opened_on: ?string, closed_on: ?string}  $slot
     */
    private function assignSlot(ClientMatter $matter, int $number, array $slot): void
    {
        $matter->{'lmt_ad'.$number.'_publication'} = $slot['publication'] !== '' ? $slot['publication'] : null;
        $matter->{'lmt_ad'.$number.'_opened_on'} = $slot['opened_on'];
        $matter->{'lmt_ad'.$number.'_closed_on'} = $slot['closed_on'];
        if ($slot['publication'] === '' && $slot['opened_on'] === null) {
            $matter->{'lmt_ad'.$number.'_document_id'} = null;
        }
    }

    /**
     * @param  array<string, mixed>  $files
     * @return array<string, UploadedFile>
     */
    private function uploadsFrom(array $files): array
    {
        $uploads = [];
        foreach (['ad1', 'ad2'] as $key) {
            $file = $files[$key] ?? null;
            if ($file instanceof UploadedFile) {
                $uploads[$key] = $file;
            }
        }

        return $uploads;
    }

    /**
     * @return array{ok: bool, status: int, message: string, uploaded: bool}
     */
    private function ok(string $message, bool $uploaded = false): array
    {
        return ['ok' => true, 'status' => 200, 'message' => $message, 'uploaded' => $uploaded];
    }

    /**
     * @return array{ok: bool, status: int, message: string, uploaded: bool}
     */
    private function fail(int $status, string $message): array
    {
        return ['ok' => false, 'status' => $status, 'message' => $message, 'uploaded' => false];
    }

    private function blankToNull(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = is_string($value) ? trim($value) : $value;

        return $value === '' ? null : $value;
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
