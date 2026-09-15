<?php

namespace App\Http\Requests\StaffFileTime;

use Illuminate\Foundation\Http\FormRequest;

class DoneStaffFileTimeRequest extends FormRequest
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
            'confirmed_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'clock_seconds' => ['nullable', 'integer', 'min:0', 'max:172800'],
        ];
    }
}
