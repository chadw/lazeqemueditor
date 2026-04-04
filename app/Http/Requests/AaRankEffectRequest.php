<?php

namespace App\Http\Requests;

class AaRankEffectRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rank_id' => 'required|integer',
            'slot' => 'integer|nullable',
            'effect_id' => 'integer|nullable',
            'base1' => 'integer|nullable',
            'base2' => 'integer|nullable',
        ];
    }
}
