<?php

namespace App\Http\Requests;

class ForceCompleteCharacterAchievementRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirm_offline_completion' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_offline_completion.accepted' => 'You must acknowledge the offline-completion warning.',
        ];
    }
}
