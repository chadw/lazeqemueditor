<?php

namespace App\Http\Requests;

class AaRankPrereqRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rank_id' => 'required|integer',
            'aa_id' => 'required|integer',
            'points' => 'required|integer',
        ];
    }
}
