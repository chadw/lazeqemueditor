<?php

namespace App\Http\Requests;

class GuildRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:32|nullable',
            'leader' => 'integer|min:1|max:2147483647|nullable',
            'minstatus' => 'integer|min:-2|max:32767|nullable',
            'motd' => 'required|string',
            'tribute' => 'integer|min:0|max:4294967295|nullable',
            'motd_setter' => 'string|max:64|nullable',
            'channel' => 'string|max:128|nullable',
            'url' => 'string|max:512|nullable',
            'favor' => 'integer|min:0|max:4294967295|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'leader' => $this->defaultInt('leader', 0),
            'minstatus' => $this->defaultInt('minstatus', 0),
            'motd' => $this->defaultString('motd', ''),
            'tribute' => $this->defaultInt('tribute', 0),
            'motd_setter' => $this->defaultString('motd_setter', ''),
            'channel' => $this->defaultString('channel', ''),
            'url' => $this->defaultString('url', ''),
            'favor' => $this->defaultInt('favor', 0),
        ]);
    }
}
