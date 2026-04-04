<?php

namespace App\Http\Requests;

class SpawnEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'spawngroupID' => 'integer|min:-2147483648|max:2147483647|nullable',
            'npcID' => 'integer|min:-2147483648|max:2147483647|nullable',
            'chance' => 'integer|min:-32768|max:32767|nullable',
            'condition_value_filter' => 'integer|min:-8388608|max:8388607|nullable',
            'min_time' => 'integer|min:-32768|max:32767|nullable',
            'max_time' => 'integer|min:-32768|max:32767|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'spawngroupID' => $this->defaultInt('spawngroupID', 0),
            'npcID' => $this->defaultInt('npcID', 0),
            'chance' => $this->defaultInt('chance', 0),
            'condition_value_filter' => $this->defaultInt('condition_value_filter', 1),
            'min_time' => $this->defaultInt('min_time', 0),
            'max_time' => $this->defaultInt('max_time', 0),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
