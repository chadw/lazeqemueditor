<?php

namespace App\Http\Requests;

class ChatRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:64|nullable',
            'owner' => 'string|max:64|nullable',
            'password' => 'string|max:64|nullable',
            'minstatus' => 'integer|min:0|max:255|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'owner' => $this->defaultString('owner', ''),
            'password' => $this->defaultString('password', ''),
            'minstatus' => $this->defaultInt('minstatus', 0),
        ]);
    }
}
