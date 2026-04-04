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
            'npc_spells_effects_id' => 'integer|nullable',
            'spell_effect_id' => 'integer|nullable',
            'minlevel' => 'integer|nullable',
            'maxlevel' => 'integer|nullable',
            'se_base' => 'integer|nullable',
            'se_limit' => 'integer|nullable',
            'se_max' => 'integer|nullable',
        ];
    }
}
