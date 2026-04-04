<?php

namespace App\Http\Requests;

class LootDropRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:255|nullable',
            'min_expansion' => 'integer|nullable',
            'max_expansion' => 'integer|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => (string) ($this->input('name') ?? 'System Created (' . now()->format('Y-m-d') . ')'),
            'min_expansion'   => $this->defaultInt('min_expansion', -1),
            'max_expansion'   => $this->defaultInt('max_expansion', -1),
            'content_flags' => (string) $this->input('content_flags', ''),
            'content_flags_disabled' => (string) $this->input('content_flags_disabled', ''),
        ]);
    }
}
