<?php

namespace App\Http\Requests;

class AuraRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|integer|min:0|max:2147483647',
            'npc_type' => 'required|integer|min:0|max:2147483647',
            'name' => 'required|string|max:64',
            'spell_id' => 'required|integer|min:0|max:2147483647',
            'distance' => 'integer|min:0|max:2147483647|nullable',
            'aura_type' => 'integer|min:0|max:2147483647|nullable',
            'spawn_type' => 'integer|min:0|max:2147483647|nullable',
            'movement' => 'integer|min:0|max:2147483647|nullable',
            'duration' => 'integer|min:0|max:2147483647|nullable',
            'icon' => 'integer|min:-1|max:2147483647|nullable',
            'cast_time' => 'integer|min:-1|max:2147483647|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->defaultInt('type', 0),
            'npc_type' => $this->defaultInt('npc_type', 0),
            'name' => $this->defaultString('name', ''),
            'spell_id' => $this->defaultInt('spell_id', 0),
            'distance' => $this->defaultInt('distance', 60),
            'aura_type' => $this->defaultInt('aura_type', 1),
            'spawn_type' => $this->defaultInt('spawn_type', 0),
            'movement' => $this->defaultInt('movement', 0),
            'duration' => $this->defaultInt('duration', 5400),
            'icon' => $this->defaultInt('icon', -1),
            'cast_time' => $this->defaultInt('cast_time', 0),
        ]);
    }
}
