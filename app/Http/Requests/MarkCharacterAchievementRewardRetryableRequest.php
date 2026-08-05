<?php

namespace App\Http\Requests;

class MarkCharacterAchievementRewardRetryableRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirm_retry' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_retry.accepted' => 'You must explicitly accept the duplicate-delivery risk before retrying this ledger.',
        ];
    }
}
