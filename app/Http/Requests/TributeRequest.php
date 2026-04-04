<?php

namespace App\Http\Requests;

class TributeRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'sometimes|integer|min:0|max:4294967295',
            'unknown' => 'integer|min:0|max:4294967295|nullable',
            'name' => 'string|max:255|nullable',
            'descr' => 'required|string',
            'isguild' => 'integer|min:0|max:127|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'unknown' => $this->defaultInt('unknown', 5),
            'name' => $this->defaultString('name', ''),
            'descr' => $this->defaultString('descr', ''),
            'isguild' => $this->defaultInt('isguild', 0),
        ]);
    }
}
