<?php

namespace App\Http\Requests\StaffMatterSession;

use Illuminate\Foundation\Http\FormRequest;

class IdleCutStaffMatterSessionRequest extends FormRequest
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
            'idle_started_at' => ['required', 'date'],
        ];
    }
}
