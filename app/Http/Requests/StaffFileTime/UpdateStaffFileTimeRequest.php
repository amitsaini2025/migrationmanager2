<?php

namespace App\Http\Requests\StaffFileTime;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffFileTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->exists('admin')) {
            $this->merge(['admin' => $this->boolean('admin')]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'client_matter_id' => ['nullable', 'integer', 'exists:client_matters,id'],
            'admin' => ['sometimes', 'boolean'],
            'clock_seconds' => ['nullable', 'integer', 'min:0', 'max:172800'],
        ];
    }
}
