<?php

namespace App\Http\Requests;

class UpdateLootTableEntryRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // loottable_entries fields
            'entry.droplimit'   => 'required|integer|min:0',
            'entry.mindrop'     => 'required|integer|min:0',
            'entry.multiplier'  => 'required|integer|min:1',
            'entry.probability' => 'required|numeric|min:0|max:100',

            // lootdrop fields
            'lootdrop.name'                    => 'required|string|max:255',
            'lootdrop.min_expansion'           => 'nullable|integer',
            'lootdrop.max_expansion'           => 'nullable|integer',
            'lootdrop.content_flags'           => 'nullable|string|max:100',
            'lootdrop.content_flags_disabled'  => 'nullable|string|max:100',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'lootdrop' => array_map(
                fn ($v) => $v === '' ? null : $v,
                $this->input('lootdrop', [])
            ),
        ]);
    }
}
