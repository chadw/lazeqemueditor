<?php

namespace App\Http\Requests;

class NpcSpellEffectRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|nullable',
            'parent_list' => 'integer|nullable',
        ];
    }
}
