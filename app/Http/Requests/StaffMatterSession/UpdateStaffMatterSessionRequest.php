<?php

namespace App\Http\Requests\StaffMatterSession;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffMatterSessionRequest extends FormRequest
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
        ];
    }
}
