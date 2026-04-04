<?php

namespace App\Http\Requests;

class FactionAssociationRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_1' => 'integer|nullable',
            'mod_1' => 'numeric|nullable',
            'id_2' => 'integer|nullable',
            'mod_2' => 'numeric|nullable',
            'id_3' => 'integer|nullable',
            'mod_3' => 'numeric|nullable',
            'id_4' => 'integer|nullable',
            'mod_4' => 'numeric|nullable',
            'id_5' => 'integer|nullable',
            'mod_5' => 'numeric|nullable',
            'id_6' => 'integer|nullable',
            'mod_6' => 'numeric|nullable',
            'id_7' => 'integer|nullable',
            'mod_7' => 'numeric|nullable',
            'id_8' => 'integer|nullable',
            'mod_8' => 'numeric|nullable',
            'id_9' => 'integer|nullable',
            'mod_9' => 'numeric|nullable',
            'id_10' => 'integer|nullable',
            'mod_10' => 'numeric|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id_1' => $this->defaultInt('id_1', 0),
            'id_2' => $this->defaultInt('id_2', 0),
            'id_3' => $this->defaultInt('id_3', 0),
            'id_4' => $this->defaultInt('id_4', 0),
            'id_5' => $this->defaultInt('id_5', 0),
            'id_6' => $this->defaultInt('id_6', 0),
            'id_7' => $this->defaultInt('id_7', 0),
            'id_8' => $this->defaultInt('id_8', 0),
            'id_9' => $this->defaultInt('id_9', 0),
            'id_10' => $this->defaultInt('id_10', 0),
            'mod_1' => $this->defaultInt('mod_1', 0),
            'mod_2' => $this->defaultInt('mod_2', 0),
            'mod_3' => $this->defaultInt('mod_3', 0),
            'mod_4' => $this->defaultInt('mod_4', 0),
            'mod_5' => $this->defaultInt('mod_5', 0),
            'mod_6' => $this->defaultInt('mod_6', 0),
            'mod_7' => $this->defaultInt('mod_7', 0),
            'mod_8' => $this->defaultInt('mod_8', 0),
            'mod_9' => $this->defaultInt('mod_9', 0),
            'mod_10' => $this->defaultInt('mod_10', 0),
        ]);
    }
}
