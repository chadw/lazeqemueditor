<?php

namespace App\Http\Requests;

class GuildRankRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'guild_id' => 'integer|nullable',
            'rank' => 'integer|nullable',
            'title' => 'string|max:128|nullable',
        ];
    }
}
