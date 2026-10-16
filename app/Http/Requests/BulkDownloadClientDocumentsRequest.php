<?php

namespace App\Http\Requests;

use App\Models\Staff;
use App\Support\StaffClientVisibility;
use Illuminate\Foundation\Http\FormRequest;

class BulkDownloadClientDocumentsRequest extends FormRequest
{
    public const MAX_DOCUMENTS = 50;

    public function authorize(): bool
    {
        $user = $this->user('admin');
        if (! $user instanceof Staff || ! $user->hasCrmModule('84')) {
            return false;
        }

        $clientId = (int) $this->input('client_id');
        if ($clientId <= 0) {
            return false;
        }

        return StaffClientVisibility::canAccessClientOrLead($clientId, $user);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'min:1'],
            'document_ids' => ['required', 'array', 'min:1', 'max:'.self::MAX_DOCUMENTS],
            'document_ids.*' => ['required', 'integer', 'distinct', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_ids.required' => 'Select at least one document to download.',
            'document_ids.min' => 'Select at least one document to download.',
            'document_ids.max' => 'You can download at most '.self::MAX_DOCUMENTS.' documents at once.',
        ];
    }
}
