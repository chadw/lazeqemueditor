<?php

namespace App\Http\Requests;

class FactionListRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:50',
            'base' => 'integer|nullable',
            'faction_base_data.client_faction_id' => 'nullable|integer',
            'faction_base_data.min' => 'nullable|integer',
            'faction_base_data.max' => 'nullable|integer',
            'faction_base_data.unk_hero1' => 'nullable|integer',
            'faction_base_data.unk_hero2' => 'nullable|integer',
            'faction_base_data.unk_hero3' => 'nullable|integer',
        ];
    }

    protected function prepareForValidation(): void
    {
        $base = $this->input('faction_base_data', []);

        $this->merge([
            'base' => $this->defaultInt('base', 0),

            'faction_base_data' => [
                'client_faction_id' => $base['client_faction_id'] ?? null,

                'min' => isset($base['min']) && $base['min'] !== '' ? (int) $base['min'] : null,
                'max' => isset($base['max']) && $base['max'] !== '' ? (int) $base['max'] : null,
                'unk_hero1' => isset($base['unk_hero1']) && $base['unk_hero1'] !== '' ? (int) $base['unk_hero1'] : null,
                'unk_hero2' => isset($base['unk_hero2']) && $base['unk_hero2'] !== '' ? (int) $base['unk_hero2'] : null,
                'unk_hero3' => isset($base['unk_hero3']) && $base['unk_hero3'] !== '' ? (int) $base['unk_hero3'] : null,
            ],
        ]);
    }
}
