<?php

namespace App\Http\Requests;

class NpcEmoteRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'emoteid' => 'integer|nullable',
            'event_' => 'integer|nullable',
            'type' => 'integer|nullable',
            'text' => 'required|string|max:512',
        ];
    }
}
