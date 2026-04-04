<?php

namespace App\Http\Requests;

class LootDropEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lootdrop_id' => 'integer|min:0|max:4294967295|nullable',
            'item_id' => 'required|integer|exists:eqemu.items,id',
            'item_charges' => 'integer|min:0|max:65535|nullable',
            'equip_item' => 'integer|min:0|max:255|nullable',
            'chance' => 'numeric|nullable',
            'disabled_chance' => 'numeric|nullable',
            'trivial_min_level' => 'integer|min:0|max:65535|nullable',
            'trivial_max_level' => 'integer|min:0|max:65535|nullable',
            'multiplier' => 'integer|min:0|max:255|nullable',
            'npc_min_level' => 'integer|min:0|max:65535|nullable',
            'npc_max_level' => 'integer|min:0|max:65535|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'lootdrop_id' => $this->defaultInt('lootdrop_id', 0),
            'item_charges' => $this->defaultInt('item_charges', 1),
            'equip_item' => $this->defaultInt('equip_item', 0),
            'chance' => $this->defaultFloat('chance', 1),
            'disabled_chance' => $this->defaultFloat('disabled_chance', 0),
            'trivial_min_level' => $this->defaultInt('trivial_min_level', 0),
            'trivial_max_level' => $this->defaultInt('trivial_max_level', 0),
            'multiplier' => $this->defaultInt('multiplier', 1),
            'npc_min_level' => $this->defaultInt('npc_min_level', 0),
            'npc_max_level' => $this->defaultInt('npc_max_level', 0),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
