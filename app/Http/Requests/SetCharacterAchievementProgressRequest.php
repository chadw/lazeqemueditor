<?php

namespace App\Http\Requests;

class SetCharacterAchievementProgressRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_count' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ];
    }
}
