<?php

namespace App\Http\Requests;

class PlayerEventLogSettingRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'field' => 'required|in:event_enabled,etl_enabled,retention_days,discord_webhook_id',
            'value' => 'nullable',
        ];
    }
}
