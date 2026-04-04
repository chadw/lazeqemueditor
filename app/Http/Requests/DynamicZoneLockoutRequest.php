<?php

namespace App\Http\Requests;

class DynamicZoneLockoutRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dynamic_zone_id' => 'required|integer|min:0|max:4294967295',
            'event_name' => 'required|string|max:256',
            'expire_time' => 'date|nullable',
            'duration' => 'integer|min:0|max:4294967295|nullable',
            'from_expedition_uuid' => 'sometimes|string|max:36',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'dynamic_zone_id' => $this->defaultInt('dynamic_zone_id', 0),
            'event_name' => $this->defaultString('event_name', ''),
            'expire_time' => $this->defaultString('expire_time', ''),
            'duration' => $this->defaultInt('duration', 0),
            'from_expedition_uuid' => $this->defaultString('from_expedition_uuid', ''),
        ]);
    }
}
