<?php

namespace App\Http\Requests;

class AlternateCurrencyRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'item_id' => 'required|integer|min:1|max:2147483647',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'item_id' => $this->defaultInt('item_id', 0),
        ]);
    }
}
