<?php

namespace App\Http\Requests;

class ResetCharacterAchievementRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirm_reset' => ['required', 'accepted'],
            'reset_rewards' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_reset.accepted' => 'You must explicitly confirm the achievement-state reset.',
        ];
    }
}
