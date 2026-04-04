<?php

namespace App\Http\Requests;

class VariableRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'varname' => 'string|max:25|nullable',
            'value' => 'required|string',
            'information' => 'sometimes|string',
            'ts' => 'date|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'varname' => $this->defaultString('varname', ''),
            'value' => $this->defaultString('value', ''),
            'information' => $this->defaultString('information', ''),
            'ts' => $this->defaultString('ts', ''),
        ]);
    }
}
