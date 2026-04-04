<?php

namespace App\Http\Requests;

class AaAbilityRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'category' => 'integer|min:-1|max:2147483647|nullable',
            'classes' => 'integer|min:1|max:65535|nullable',
            'races' => 'integer|min:1|max:65535|nullable',
            'drakkin_heritage' => 'integer|min:0|max:127|nullable',
            'deities' => 'integer|min:1|max:131071|nullable',
            'status' => 'integer|min:-2|max:2147483647|nullable',
            'type' => 'integer|min:0|max:2147483647|nullable',
            'charges' => 'integer|min:0|max:2147483647|nullable',
            'grant_only' => 'integer|min:0|max:1|nullable',
            'first_rank_id' => 'integer|min:-1|max:2147483647|nullable',
            'enabled' => 'integer|min:0|max:1|nullable',
            'reset_on_death' => 'integer|min:0|max:1|nullable',
            'auto_grant_enabled' => 'integer|min:0|max:1|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'category' => $this->defaultInt('category', -1),
            'classes' => $this->defaultInt('classes', 65535),
            'races' => $this->defaultInt('races', 65535),
            'drakkin_heritage' => $this->defaultInt('drakkin_heritage', 127),
            'deities' => $this->defaultInt('deities', 131071),
            'status' => $this->defaultInt('status', 0),
            'type' => $this->defaultInt('type', 0),
            'charges' => $this->defaultInt('charges', 0),
            'grant_only' => $this->defaultInt('grant_only', 0),
            'first_rank_id' => $this->defaultInt('first_rank_id', -1),
            'enabled' => $this->defaultInt('enabled', 1),
            'reset_on_death' => $this->defaultInt('reset_on_death', 0),
            'auto_grant_enabled' => $this->defaultInt('auto_grant_enabled', 0),
        ]);
    }
}
