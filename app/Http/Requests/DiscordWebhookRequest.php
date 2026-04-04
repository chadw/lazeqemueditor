<?php

namespace App\Http\Requests;

class DiscordWebhookRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'webhook_name' => 'string|max:100|nullable',
            'webhook_url' => 'string|max:255|nullable',
            'deleted_at' => 'date|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'webhook_name' => $this->defaultString('webhook_name', ''),
            'webhook_url' => $this->defaultString('webhook_url', ''),
        ]);
    }
}
