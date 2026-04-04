<?php

namespace App\Http\Requests;

class DbStrRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $typeRule = $this->isMethod('post') ? 'required|integer' : 'nullable|integer';

        return [
            'id' => 'nullable|integer',
            'type' => $typeRule,
            'value' => 'required|string',
        ];
    }
}
