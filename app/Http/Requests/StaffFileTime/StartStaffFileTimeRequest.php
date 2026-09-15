<?php

namespace App\Http\Requests\StaffFileTime;

use App\Models\StaffFileTimeEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StartStaffFileTimeRequest extends FormRequest
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
            'kind' => ['required', 'string', Rule::in(StaffFileTimeEntry::kinds())],
            'title' => ['required', 'string', 'max:255'],
            'client_matter_id' => ['nullable', 'integer', 'exists:client_matters,id'],
            'admin' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $admin = $this->boolean('admin');
                $matterId = (int) $this->input('client_matter_id');
                if (! $admin && $matterId < 1) {
                    $validator->errors()->add('client_matter_id', 'Choose a matter or Admin / no file.');
                }
            },
        ];
    }
}
