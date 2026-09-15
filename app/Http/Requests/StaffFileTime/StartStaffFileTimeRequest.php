<?php

namespace App\Http\Requests\StaffFileTime;

use App\Models\StaffFileTimeEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartStaffFileTimeRequest extends FormRequest
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
            'kind' => ['required', 'string', Rule::in(StaffFileTimeEntry::kinds())],
            'title' => ['required', 'string', 'max:255'],
            'client_matter_id' => ['nullable', 'integer', 'exists:client_matters,id'],
            'admin' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $admin = filter_var($this->input('admin'), FILTER_VALIDATE_BOOLEAN);
            $matterId = (int) $this->input('client_matter_id');
            if (! $admin && $matterId < 1) {
                $validator->errors()->add('client_matter_id', 'Choose a matter or Admin / no file.');
            }
        });
    }
}
