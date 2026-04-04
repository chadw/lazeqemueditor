<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class FactionListModRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $factionId = $this->route('faction')?->id;
        $modId = $this->route('mod')?->id;

        return [
            //'faction_id' => 'required|integer',
            'mod' => 'required|integer',
            'mod_name' => [
                'required',
                'string',
                'max:16',
                Rule::unique('eqemu.faction_list_mod', 'mod_name')
                    ->where('faction_id', $factionId)
                    ->ignore($modId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'mod_name.unique' => 'This faction already has a mod with the same type and value.',
        ];
    }
}
