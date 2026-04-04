<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PetEquipmentsetEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'set_id' => 'required|integer',
            'slot' => [
                'required',
                'integer',
                Rule::unique('eqemu.pets_equipmentset_entries')
                    ->where('set_id', $this->route('set'))
                    ->ignore($this->route('slot'), 'slot')
            ],
            'item_id' => 'required|integer',
        ];
    }
}
