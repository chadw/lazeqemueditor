<?php

namespace App\Http\Requests;

class MailRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'msgid' => 'nullable',
            'charid' => 'integer|nullable',
            'timestamp' => 'integer|nullable',
            'from' => 'string|max:100|nullable',
            'subject' => 'string|max:200|nullable',
            'body' => 'required|string',
            'to' => 'required|string',
            'status' => 'integer|nullable',
        ];
    }
}
