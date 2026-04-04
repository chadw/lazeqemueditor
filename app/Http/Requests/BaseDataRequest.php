<?php

namespace App\Http\Requests;

class BaseDataRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'level' => 'required|integer|min:0|max:255',
            'class' => 'required|integer|min:0|max:255',
            'hp' => 'required|numeric',
            'mana' => 'required|numeric',
            'end' => 'required|numeric',
            'hp_regen' => 'required|numeric',
            'end_regen' => 'required|numeric',
            'hp_fac' => 'required|numeric',
            'mana_fac' => 'required|numeric',
            'end_fac' => 'required|numeric',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'level' => $this->defaultInt('level', 0),
            'class' => $this->defaultInt('class', 0),
            'hp' => $this->defaultFloat('hp', 0),
            'mana' => $this->defaultFloat('mana', 0),
            'end' => $this->defaultFloat('end', 0),
            'hp_regen' => $this->defaultFloat('hp_regen', 0),
            'end_regen' => $this->defaultFloat('end_regen', 0),
            'hp_fac' => $this->defaultFloat('hp_fac', 0),
            'mana_fac' => $this->defaultFloat('mana_fac', 0),
            'end_fac' => $this->defaultFloat('end_fac', 0),
        ]);
    }
}
