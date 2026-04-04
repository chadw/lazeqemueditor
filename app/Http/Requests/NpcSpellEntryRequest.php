<?php

namespace App\Http\Requests;

class NpcSpellEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npc_spells_id' => 'integer|nullable',
            'spellid' => 'integer|nullable',
            'type' => 'integer|nullable',
            'minlevel' => 'integer|nullable',
            'maxlevel' => 'integer|nullable',
            'manacost' => 'integer|nullable',
            'recast_delay' => 'integer|nullable',
            'priority' => 'integer|nullable',
            'resist_adjust' => 'integer|nullable',
            'min_hp' => 'integer|nullable',
            'max_hp' => 'integer|nullable',
            'min_expansion' => 'integer|nullable',
            'max_expansion' => 'integer|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }
}
