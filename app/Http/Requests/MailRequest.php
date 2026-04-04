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
            'charid' => 'integer|min:0|max:4294967295|nullable',
            'timestamp' => 'integer|min:-2147483648|max:2147483647|nullable',
            'from' => 'string|max:100|nullable',
            'subject' => 'string|max:200|nullable',
            'body' => 'required|string',
            'to' => 'required|string',
            'status' => 'integer|min:-128|max:127|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'charid' => $this->defaultInt('charid', 0),
            'timestamp' => $this->defaultInt('timestamp', 0),
            'from' => $this->defaultString('from', ''),
            'subject' => $this->defaultString('subject', ''),
            'body' => $this->defaultString('body', ''),
            'to' => $this->defaultString('to', ''),
            'status' => $this->defaultInt('status', 0),
        ]);
    }
}
