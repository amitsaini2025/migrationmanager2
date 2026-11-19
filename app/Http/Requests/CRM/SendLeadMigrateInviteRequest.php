<?php

namespace App\Http\Requests\CRM;

use App\Support\LeadMigrateInviteChannels;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendLeadMigrateInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $channel = (string) $this->input('channel');

        return [
            'channel' => ['required', 'string', Rule::in(LeadMigrateInviteChannels::channels())],
            'email' => [
                Rule::requiredIf($channel === LeadMigrateInviteChannels::CHANNEL_EMAIL),
                'nullable',
                'email',
                'max:150',
            ],
            'phone' => [
                Rule::requiredIf($channel === LeadMigrateInviteChannels::CHANNEL_SMS),
                'nullable',
                'string',
                'max:25',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter the lead email.',
            'phone.required' => 'Please enter the lead contact number.',
        ];
    }
}
