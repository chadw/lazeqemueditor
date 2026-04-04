<?php

namespace App\Http\Requests;

class CharacterExpeditionLockoutRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'character_id' => 'required|integer|min:0|max:4294967295',
            'expedition_name' => 'sometimes|string|max:128',
            'event_name' => 'required|string|max:256',
            'expire_time' => 'date|nullable',
            'duration' => 'integer|min:0|max:4294967295|nullable',
            'from_expedition_uuid' => 'sometimes|string|max:36',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'character_id' => $this->defaultInt('character_id', 0),
            'expedition_name' => $this->defaultString('expedition_name', ''),
            'event_name' => $this->defaultString('event_name', ''),
            'expire_time' => $this->defaultString('expire_time', ''),
            'duration' => $this->defaultInt('duration', 0),
            'from_expedition_uuid' => $this->defaultString('from_expedition_uuid', ''),
        ]);
    }
}
