<?php

namespace App\Http\Requests;

class PetEquipmentsetRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'set_id' => 'sometimes|integer',
            'setname' => 'string|max:30|nullable',
            'nested_set' => 'integer|nullable',
        ];

        return $rules;
    }
}
