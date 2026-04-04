<?php

namespace App\Http\Requests;

class TradeskillRecipeEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'recipe_id' => 'integer|min:1|max:2147483647|nullable',
            'template_container_id' => [
                'nullable',
                'integer',
                'min:1',
                'required_without:item_id',
            ],

            'item_id' => [
                'nullable',
                'integer',
                'min:1',
                'required_without:template_container_id',
            ],
            'successcount' => 'integer|min:0|max:127|nullable',
            'failcount' => 'integer|min:0|max:127|nullable',
            'componentcount' => 'integer|min:0|max:127|nullable',
            'salvagecount' => 'integer|min:0|max:127|nullable',
            'iscontainer' => 'boolean|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'recipe_id' => $this->defaultInt('recipe_id', 0),
            'item_id' => $this->filled('item_id')
                ? $this->defaultInt('item_id', 0)
                : null,
            'template_container_id' => $this->filled('template_container_id')
                ? $this->defaultInt('template_container_id', 0)
                : null,
            'successcount' => $this->defaultInt('successcount', 0),
            'failcount' => $this->defaultInt('failcount', 0),
            'componentcount' => $this->defaultInt('componentcount', 0),
            'salvagecount' => $this->defaultInt('salvagecount', 0),
            'iscontainer' => $this->defaultInt('iscontainer', 0),
        ]);
    }
}
