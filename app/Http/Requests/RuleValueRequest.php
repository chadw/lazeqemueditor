<?php

namespace App\Http\Requests;

class RuleValueRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ruleset_id' => 'integer|nullable',
            'rule_name' => 'string|max:64|nullable',
            'rule_value' => 'string|nullable',
            'notes' => 'string|nullable',
        ];
    }
}
