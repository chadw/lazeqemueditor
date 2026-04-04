<?php

namespace App\Http\Requests;

class TitleRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'skill_id' => 'integer|min:-1|max:127|nullable',
            'min_skill_value' => 'integer|min:-1|max:8388607|nullable',
            'max_skill_value' => 'integer|min:-1|max:8388607|nullable',
            'min_aa_points' => 'integer|min:-1|max:8388607|nullable',
            'max_aa_points' => 'integer|min:-1|max:8388607|nullable',
            'class' => 'integer|min:-1|max:127|nullable',
            'gender' => 'integer|nullable|min:-1|max:2',
            'char_id' => 'integer|min:-1|max:2147483647|nullable',
            'status' => 'integer|min:-2|max:2147483647|nullable',
            'item_id' => 'integer|min:-1|max:2147483647|nullable',
            'prefix' => 'string|max:31|nullable',
            'suffix' => 'string|max:31|nullable',
            'title_set' => 'integer|min:0|max:2147483647|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'skill_id' => $this->defaultInt('skill_id', -1),
            'min_skill_value' => $this->defaultInt('min_skill_value', -1),
            'max_skill_value' => $this->defaultInt('max_skill_value', -1),
            'min_aa_points' => $this->defaultInt('min_aa_points', -1),
            'max_aa_points' => $this->defaultInt('max_aa_points', -1),
            'class' => $this->defaultInt('class', -1),
            'gender' => $this->defaultInt('gender', -1),
            'char_id' => $this->defaultInt('char_id', -1),
            'status' => $this->defaultInt('status', -1),
            'item_id' => $this->defaultInt('item_id', -1),
            'prefix' => $this->defaultString('prefix', ''),
            'suffix' => $this->defaultString('suffix', ''),
            'title_set' => $this->defaultInt('title_set', 0),
        ]);
    }
}
