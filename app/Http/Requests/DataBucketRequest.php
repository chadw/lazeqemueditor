<?php

namespace App\Http\Requests;

class DataBucketRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'key' => 'string|max:100|nullable',
            'value' => 'string|nullable',
            'expires' => 'integer|min:0|max:4294967295|nullable',
            'account_id' => 'integer|min:0|max:18446744073709551615|nullable',
            'character_id' => 'integer|min:0|max:18446744073709551615|nullable',
            'npc_id' => 'integer|min:0|max:4294967295|nullable',
            'bot_id' => 'integer|min:0|max:4294967295|nullable',
            'zone_id' => 'integer|min:0|max:65535|nullable',
            'instance_id' => 'integer|min:0|max:65535|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'key' => $this->defaultString('key', ''),
            'value' => $this->defaultString('value', ''),
            'expires' => $this->defaultInt('expires', 0),
            'account_id' => $this->defaultInt('account_id', 0),
            'character_id' => $this->defaultInt('character_id', 0),
            'npc_id' => $this->defaultInt('npc_id', 0),
            'bot_id' => $this->defaultInt('bot_id', 0),
            'zone_id' => $this->defaultInt('zone_id', 0),
            'instance_id' => $this->defaultInt('instance_id', 0),
        ]);
    }
}
