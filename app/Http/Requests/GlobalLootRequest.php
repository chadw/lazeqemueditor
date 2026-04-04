<?php

namespace App\Http\Requests;

class GlobalLootRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => 'required|string|max:255',
            'loottable_id' => 'nullable|integer',
            'enabled' => 'integer|nullable',
            'min_level' => 'integer|nullable|min:0',
            'max_level' => 'integer|nullable|min:0|max:100',
            'rare' => 'integer|nullable',
            'raid' => 'integer|nullable',
            'race' => 'array|nullable',
            'race.*' => 'numeric',
            'class' => 'array|nullable',
            'class.*' => 'numeric',
            'bodytype' => 'array|nullable',
            'bodytype.*' => 'numeric',
            'zone' => 'array|nullable',
            'zone.*' => 'numeric',
            'hot_zone' => 'integer|nullable',
            'min_expansion' => 'integer|nullable',
            'max_expansion' => 'integer|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    /**
     * Force the array into a pipe string AFTER validation passes.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return void
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        if (isset($validated['race']) && is_array($validated['race'])) {
            $validated['race'] = implode('|', $validated['race']);
        }

        if (isset($validated['class']) && is_array($validated['class'])) {
            $validated['class'] = implode('|', $validated['class']);
        }

        if (isset($validated['bodytype']) && is_array($validated['bodytype'])) {
            $validated['bodytype'] = implode('|', $validated['bodytype']);
        }

        if (isset($validated['zone']) && is_array($validated['zone'])) {
            $validated['zone'] = implode('|', $validated['zone']);
        }

        return $validated;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'min_level' => $this->defaultInt('min_level', 0),
            'max_level' => $this->defaultInt('max_level', 0),
            'enabled'   => $this->defaultInt('enabled', 1),
        ]);
    }
}
