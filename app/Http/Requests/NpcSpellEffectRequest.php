<?php

namespace App\Http\Requests;

class NpcSpellEffectRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|nullable',
            'parent_list' => 'integer|min:0|max:4294967295|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'parent_list' => $this->defaultInt('parent_list', 0),
        ]);
    }
}
