<?php

namespace App\Http\Requests;

class NpcSpellEffectEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npc_spells_effects_id' => 'integer|min:1|max:2147483647|nullable',
            'spell_effect_id' => 'integer|min:0|max:32767|nullable',
            'minlevel' => 'integer|min:0|max:255|nullable',
            'maxlevel' => 'integer|min:0|max:255|nullable',
            'se_base' => 'integer|min:-2147483648|max:2147483647|nullable',
            'se_limit' => 'integer|min:-2147483648|max:2147483647|nullable',
            'se_max' => 'integer|min:-2147483648|max:2147483647|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'npc_spells_effects_id' => $this->defaultInt('npc_spells_effects_id', 0),
            'spell_effect_id' => $this->defaultInt('spell_effect_id', 0),
            'minlevel' => $this->defaultInt('minlevel', 0),
            'maxlevel' => $this->defaultInt('maxlevel', 255),
            'se_base' => $this->defaultInt('se_base', 0),
            'se_limit' => $this->defaultInt('se_limit', 0),
            'se_max' => $this->defaultInt('se_max', 0),
        ]);
    }
}
