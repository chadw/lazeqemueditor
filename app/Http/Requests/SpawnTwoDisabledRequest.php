<?php

namespace App\Http\Requests;

class SpawnTwoDisabledRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'spawn2_id' => 'integer|min:0|max:2147483647|nullable',
            'instance_id' => 'integer|min:0|max:2147483647|nullable',
            'disabled' => 'boolean|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'spawn2_id' => $this->defaultInt('spawn2_id', 0),
            'instance_id' => $this->defaultInt('instance_id', 0),
            'disabled' => $this->defaultInt('disabled', 0),
        ]);
    }
}
