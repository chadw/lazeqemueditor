<?php

namespace App\Http\Requests;

class ForageRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zoneid' => 'integer|min:0|max:2147483647|nullable',
            'Itemid' => 'integer|min:0|max:2147483647|nullable',
            'level' => 'integer|min:0|max:32767|nullable',
            'chance' => 'integer|min:0|max:32767|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'zoneid' => $this->defaultInt('zoneid', 0),
            'Itemid' => $this->defaultInt('Itemid', 0),
            'level' => $this->defaultInt('level', 0),
            'chance' => $this->defaultInt('chance', 0),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
