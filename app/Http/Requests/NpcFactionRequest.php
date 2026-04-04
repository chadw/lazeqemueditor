<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class NpcFactionRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npc_id' => [
                'nullable',
                'integer',
                Rule::exists('eqemu.npc_types', 'id'),
            ],
            'name' => 'string|nullable',
            'primaryfaction' => 'integer|min:0|max:2147483647|nullable',
            'ignore_primary_assist' => 'integer|min:0|max:127|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'primaryfaction' => $this->defaultInt('primaryfaction', 0),
            'ignore_primary_assist' => $this->defaultInt('ignore_primary_assist', 0),
        ]);
    }
}
