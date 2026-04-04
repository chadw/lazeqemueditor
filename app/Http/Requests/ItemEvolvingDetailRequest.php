<?php

namespace App\Http\Requests;

class ItemEvolvingDetailRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return array(
            'item_evo_id' => 'integer|nullable',
            'item_evolve_level' => 'integer|nullable',
            'item_id' => 'integer|nullable',
            'type' => 'integer|nullable',
            //'sub_type' => 'array|string|max:200|nullable',
            'sub_type' => ['nullable'],
            'required_amount' => 'integer|nullable',
        );
    }

    protected function prepareForValidation(): void
    {
        $subType = $this->input('sub_type');
        if (is_array($subType)) {
            $processed = collect($subType)
                ->flatten()
                ->filter(function ($v) {
                    return !is_null($v) && $v !== '';
                })
                ->map(function ($v) {
                    return is_numeric($v) ? (string) intval($v) : (string) $v;
                })
                ->unique()
                ->values();

            if ($processed->isEmpty()) {
                $subType = '1';
            } else {
                $subType = $processed->implode('.');
            }
        }

        $this->merge([
            'item_evo_id' => $this->defaultInt('item_evo_id', 0),
            'item_evolve_level' => $this->defaultInt('item_evolve_level', 0),
            'item_id' => $this->defaultInt('item_id', 0),
            'type' => $this->defaultInt('type', 0),
            'sub_type' => $subType ?? '0',
            'required_amount' => $this->defaultInt('required_amount', 0),
        ]);
    }
}
