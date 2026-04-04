<?php

namespace App\Http\Requests;

class CharacterAltCurrencyRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'char_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'amount' => 'required|integer',
        ];
    }
}
