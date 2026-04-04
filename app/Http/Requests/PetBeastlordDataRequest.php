<?php

namespace App\Http\Requests;

class PetBeastlordDataRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'player_race' => 'integer|nullable',
            'pet_race' => 'integer|nullable',
            'texture' => 'integer|nullable',
            'helm_texture' => 'integer|nullable',
            'gender' => 'integer|nullable',
            'size_modifier' => 'numeric|nullable',
            'face' => 'integer|nullable',
        ];
    }
}
