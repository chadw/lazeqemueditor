<?php

namespace App\Http\Requests;

class BookRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:30|nullable',
            'txtfile' => 'required|string',
            'language' => 'integer|min:-1|max:2147483647|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'txtfile' => $this->defaultString('txtfile', ''),
            'language' => $this->defaultInt('language', 0),
        ]);
    }
}
