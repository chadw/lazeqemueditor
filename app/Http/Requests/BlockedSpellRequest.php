<?php

namespace App\Http\Requests;

class BlockedSpellRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return array(
            'spellid' => 'integer|min:0|max:16777215|nullable',
            'type' => 'integer|min:0|max:2|nullable',
            'zoneid' => 'integer|min:0|max:2147483647|nullable',
            'x' => 'numeric|nullable',
            'y' => 'numeric|nullable',
            'z' => 'numeric|nullable',
            'x_diff' => 'numeric|nullable',
            'y_diff' => 'numeric|nullable',
            'z_diff' => 'numeric|nullable',
            'message' => 'string|max:255|nullable',
            'description' => 'string|max:255|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'spellid' => $this->defaultInt('spellid', 0),
            'type' => $this->defaultInt('type', 0),
            'zoneid' => $this->defaultInt('zoneid', 0),
            'x' => $this->defaultFloat('x', 0),
            'y' => $this->defaultFloat('y', 0),
            'z' => $this->defaultFloat('z', 0),
            'x_diff' => $this->defaultFloat('x_diff', 0),
            'y_diff' => $this->defaultFloat('y_diff', 0),
            'z_diff' => $this->defaultFloat('z_diff', 0),
            'message' => $this->defaultString('message', ''),
            'description' => $this->defaultString('description', ''),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
