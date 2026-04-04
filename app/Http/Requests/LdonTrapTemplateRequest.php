<?php

namespace App\Http\Requests;

class LdonTrapTemplateRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'integer|min:0|max:255|nullable',
            'spell_id' => 'integer|min:0|max:65535|nullable',
            'skill' => 'integer|min:0|max:65535|nullable',
            'locked' => 'integer|min:0|max:255|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->defaultInt('type', 1),
            'spell_id' => $this->defaultInt('spell_id', 0),
            'skill' => $this->defaultInt('skill', 0),
            'locked' => $this->defaultInt('locked', 0),
        ]);
    }
}
