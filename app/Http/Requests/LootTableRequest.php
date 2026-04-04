<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LootTableRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npc_id' => [
                'nullable',
                'integer',
                Rule::exists('eqemu.npc_types', 'id'),
            ],
            'name' => 'string|max:255|nullable',
            'mincash' => 'integer|nullable',
            'maxcash' => 'integer|nullable',
            'avgcoin' => 'integer|nullable',
            'done' => 'integer|nullable',
            'min_expansion' => 'integer|nullable',
            'max_expansion' => 'integer|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => (string) ($this->input('name') ?? 'GLB System Created (' . now()->format('Y-m-d') . ')'),
            'mincash' => $this->defaultInt('mincash', 0),
            'maxcash' => $this->defaultInt('maxcash', 0),
            'avgcoin' => $this->defaultInt('avgcoin', 0),
            'done' => $this->defaultInt('done', 0),
            'min_expansion'   => $this->defaultInt('min_expansion', -1),
            'max_expansion'   => $this->defaultInt('max_expansion', -1),
            'content_flags' => (string) $this->input('content_flags', ''),
            'content_flags_disabled' => (string) $this->input('content_flags_disabled', ''),
        ]);
    }
}
