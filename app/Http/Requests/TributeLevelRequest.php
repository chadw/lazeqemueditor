<?php

namespace App\Http\Requests;

class TributeLevelRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tribute_id' => 'required|integer',
            'level' => 'sometimes|integer',
            'cost' => 'integer|nullable',
            'item_id' => 'integer|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $routeTributeId = $this->route('tribute_id');
        $routeLevel     = $this->route('level');

        $this->merge([
            // If it's in the route, use that. Otherwise use input, fallback to 0.
            'tribute_id' => $this->defaultInt('tribute_id', (int)($routeTributeId ?? $this->input('tribute_id', 0))),
            'level'      => $this->defaultInt('level', (int)($routeLevel ?? $this->input('level', 0))),
            'cost'       => $this->defaultInt('cost', (int)$this->input('cost', 0)),
            'item_id'    => $this->defaultInt('item_id', (int)$this->input('item_id', 0)),
        ]);
    }
}
