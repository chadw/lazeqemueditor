<?php

namespace App\Http\Requests;

class StartingItemRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'class_list'       => ['nullable', 'array'],
            'class_list.*'     => ['integer'],
            'race_list'        => ['nullable', 'array'],
            'race_list.*'      => ['integer'],
            'deity_list'       => ['nullable', 'array'],
            'deity_list.*'     => ['integer'],
            'zone_id_list'     => ['nullable', 'array'],
            'zone_id_list.*'   => ['integer'],
            'item_id' => 'integer|nullable',
            'item_charges' => 'integer|nullable',
            'augment_one' => 'integer|nullable',
            'augment_two' => 'integer|nullable',
            'augment_three' => 'integer|nullable',
            'augment_four' => 'integer|nullable',
            'augment_five' => 'integer|nullable',
            'augment_six' => 'integer|nullable',
            'status' => 'integer|nullable',
            'inventory_slot' => 'integer|nullable',
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

        if (isset($validated['class_list']) && is_array($validated['class_list'])) {
            $validated['class_list'] = implode('|', $validated['class_list']);
        }

        if (isset($validated['race_list']) && is_array($validated['race_list'])) {
            $validated['race_list'] = implode('|', $validated['race_list']);
        }

        if (isset($validated['deity_list']) && is_array($validated['deity_list'])) {
            $validated['deity_list'] = implode('|', $validated['deity_list']);
        }

        if (isset($validated['zone_id_list']) && is_array($validated['zone_id_list'])) {
            $validated['zone_id_list'] = implode('|', $validated['zone_id_list']);
        }

        return $validated;
    }
}
