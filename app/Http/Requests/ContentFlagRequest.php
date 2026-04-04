<?php

namespace App\Http\Requests;

class ContentFlagRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'flag_name' => 'string|max:75|nullable',
            'enabled' => 'integer|min:0|max:1|nullable',
            'notes' => 'string|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'flag_name' => $this->defaultString('flag_name', ''),
            'enabled' => $this->defaultInt('enabled', 0),
            'notes' => $this->defaultString('notes', ''),
        ]);
    }
}
