<?php

namespace App\Http\Requests;

class GridRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zoneid' => 'integer|min:1|max:2147483647|nullable',
            'type' => 'integer|min:0|max:9|nullable',
            'type2' => 'integer|min:0|max:2|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'zoneid' => $this->defaultInt('zoneid', 0),
            'type' => $this->defaultInt('type', 0),
            'type2' => $this->defaultInt('type2', 0),
        ]);
    }
}
