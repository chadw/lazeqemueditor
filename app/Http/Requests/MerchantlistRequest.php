<?php

namespace App\Http\Requests;

class MerchantlistRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'merchantid' => 'integer|nullable',
            'slot' => 'integer|nullable',
            'item' => 'integer|nullable',
            'faction_required' => 'integer|nullable',
            'level_required' => 'integer|nullable',
            'min_status' => 'integer|nullable',
            'max_status' => 'integer|nullable',
            'alt_currency_cost' => 'integer|nullable',
            'classes_required' => 'integer|nullable',
            'probability' => 'integer|nullable',
            'bucket_name' => 'string|max:100|nullable',
            'bucket_value' => 'string|max:100|nullable',
            'bucket_comparison' => 'integer|nullable',
            'min_expansion' => 'integer|nullable',
            'max_expansion' => 'integer|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'faction_required' => $this->defaultInt('faction_required', -100),
            'level_required' => $this->defaultInt('level_required', 0),
            'min_status' => $this->defaultInt('min_status', 0),
            'max_status' => $this->defaultInt('max_status', 255),
            'alt_currency_cost' => $this->defaultInt('alt_currency_cost', 0),
            'classes_required' => $this->defaultInt('classes_required', 65535),
            'probability' => $this->defaultInt('probability', 100),
            'bucket_name' => (string) $this->input('bucket_name', ''),
            'bucket_value' => (string) $this->input('bucket_value', ''),
            'bucket_comparison' => $this->defaultInt('bucket_comparison', 0),
        ]);
    }
}
