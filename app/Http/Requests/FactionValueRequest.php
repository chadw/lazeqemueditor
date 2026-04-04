<?php

namespace App\Http\Requests;

class FactionValueRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'char_id' => 'integer|nullable',
            'faction_id' => 'integer|nullable',
            'current_value' => 'integer|nullable',
            'temp' => 'integer|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'current_value' => $this->defaultInt('current_value', 0),
            'temp' => $this->defaultInt('temp', 0),
        ]);
    }
}
