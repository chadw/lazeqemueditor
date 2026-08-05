<?php

namespace App\Http\Requests;

class DiscardCharacterAchievementMutationRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirm_discard' => ['required', 'accepted'],
        ];
    }
}
