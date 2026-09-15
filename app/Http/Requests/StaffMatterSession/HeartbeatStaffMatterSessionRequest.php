<?php

namespace App\Http\Requests\StaffMatterSession;

use Illuminate\Foundation\Http\FormRequest;

class HeartbeatStaffMatterSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'min:1'],
            'client_matter_id' => ['nullable', 'integer', 'min:1'],
            'focused_seconds' => ['required', 'integer', 'min:0', 'max:86400'],
        ];
    }
}
