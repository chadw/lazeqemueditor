<?php

namespace App\Http\Requests;

class GuildMemberRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'char_id' => 'integer|min:0|max:2147483647|nullable',
            'guild_id' => 'integer|min:0|max:16777215|nullable',
            'rank' => 'integer|min:0|max:255|nullable',
            'tribute_enable' => 'integer|min:0|max:255|nullable',
            'total_tribute' => 'integer|min:0|max:4294967295|nullable',
            'last_tribute' => 'integer|min:0|max:4294967295|nullable',
            'banker' => 'integer|min:0|max:255|nullable',
            'public_note' => 'required|string',
            'alt' => 'integer|min:0|max:255|nullable',
            'online' => 'integer|min:0|max:255|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'char_id' => $this->defaultInt('char_id', 0),
            'guild_id' => $this->defaultInt('guild_id', 0),
            'rank' => $this->defaultInt('rank', 0),
            'tribute_enable' => $this->defaultInt('tribute_enable', 0),
            'total_tribute' => $this->defaultInt('total_tribute', 0),
            'last_tribute' => $this->defaultInt('last_tribute', 0),
            'banker' => $this->defaultInt('banker', 0),
            'public_note' => $this->defaultString('public_note', ''),
            'alt' => $this->defaultInt('alt', 0),
            'online' => $this->defaultInt('online', 0),
        ]);
    }
}
