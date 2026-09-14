<?php

namespace App\Http\Requests\CRM;

use App\Support\ClientDetailVerificationFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendClientDetailVerificationLinkRequest extends FormRequest
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
        return [
            'client_id' => ['required', 'integer', 'min:1'],
            'channel' => ['required', 'string', Rule::in([
                ClientDetailVerificationFields::CHANNEL_EMAIL,
                ClientDetailVerificationFields::CHANNEL_SMS,
            ])],
        ];
    }
}
