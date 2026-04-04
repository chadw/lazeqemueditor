<?php

namespace App\Http\Requests;

class ParcelRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'char_id' => 'integer|nullable',
            'item_id' => 'integer|nullable',
            'aug_slot_1' => 'integer|nullable',
            'aug_slot_2' => 'integer|nullable',
            'aug_slot_3' => 'integer|nullable',
            'aug_slot_4' => 'integer|nullable',
            'aug_slot_5' => 'integer|nullable',
            'aug_slot_6' => 'integer|nullable',
            'slot_id' => 'integer|nullable',
            'quantity' => 'integer|nullable',
            'evolve_amount' => 'integer|nullable',
            'from_name' => 'string|max:64|nullable',
            'note' => 'string|max:1024|nullable',
            'sent_date' => 'date|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'aug_slot_1'    => $this->defaultInt('aug_slot_1', 0),
            'aug_slot_2'    => $this->defaultInt('aug_slot_2', 0),
            'aug_slot_3'    => $this->defaultInt('aug_slot_3', 0),
            'aug_slot_4'    => $this->defaultInt('aug_slot_4', 0),
            'aug_slot_5'    => $this->defaultInt('aug_slot_5', 0),
            'aug_slot_6'    => $this->defaultInt('aug_slot_6', 0),
            'quantity'      => $this->defaultInt('quantity', 1),
            'evolve_amount' => $this->defaultInt('evolve_amount', 1),
        ]);
    }
}
