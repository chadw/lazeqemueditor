<?php

namespace App\Http\Requests;

class FactionBaseDataRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_faction_id' => 'required|integer|min:0|max:32767',
            'min' => 'integer|min:-32768|max:32767|nullable',
            'max' => 'integer|min:-32768|max:32767|nullable',
            'unk_hero1' => 'integer|min:0|max:32767|nullable',
            'unk_hero2' => 'integer|min:0|max:32767|nullable',
            'unk_hero3' => 'integer|min:0|max:32767|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'client_faction_id' => $this->defaultInt('client_faction_id', 0),
            'min' => $this->defaultInt('min', -2000),
            'max' => $this->defaultInt('max', 2000),
            'unk_hero1' => $this->defaultInt('unk_hero1', 0),
            'unk_hero2' => $this->defaultInt('unk_hero2', 0),
            'unk_hero3' => $this->defaultInt('unk_hero3', 0),
        ]);
    }
}
