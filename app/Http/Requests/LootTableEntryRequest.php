<?php

namespace App\Http\Requests;

class LootTableEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'loottable_id' => 'integer|min:0|max:4294967295|nullable',
            'lootdrop_id' => 'integer|min:0|max:4294967295|nullable',
            'multiplier' => 'integer|min:0|max:255|nullable',
            'droplimit' => 'integer|min:0|max:255|nullable',
            'mindrop' => 'integer|min:0|max:255|nullable',
            'probability' => 'numeric|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'loottable_id' => $this->defaultInt('loottable_id', 0),
            'lootdrop_id' => $this->defaultInt('lootdrop_id', 0),
            'multiplier' => $this->defaultInt('multiplier', 1),
            'droplimit' => $this->defaultInt('droplimit', 0),
            'mindrop' => $this->defaultInt('mindrop', 0),
            'probability' => $this->defaultFloat('probability', 100),
        ]);
    }
}
