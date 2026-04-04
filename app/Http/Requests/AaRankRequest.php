<?php

namespace App\Http\Requests;

class AaRankRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'upper_hotkey_sid' => 'integer|nullable',
            'lower_hotkey_sid' => 'integer|nullable',
            'title_sid' => 'integer|nullable',
            'desc_sid' => 'integer|nullable',
            'cost' => 'integer|nullable',
            'level_req' => 'integer|nullable',
            'spell' => 'integer|nullable',
            'spell_type' => 'integer|nullable',
            'recast_time' => 'integer|nullable',
            'expansion' => 'integer|nullable',
            'prev_id' => 'integer|nullable',
            'next_id' => 'integer|nullable',
        ];
    }
}
