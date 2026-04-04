<?php

namespace App\Http\Requests;

class PetRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'string|max:64|nullable',
            'petpower' => 'integer|nullable',
            'npcID' => 'integer|nullable',
            'temp' => 'integer|nullable',
            'petcontrol' => 'integer|nullable',
            'petnaming' => 'integer|nullable',
            'monsterflag' => 'integer|nullable',
            'equipmentset' => 'integer|nullable',
        ];
    }
}
