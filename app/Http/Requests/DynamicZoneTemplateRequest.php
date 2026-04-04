<?php

namespace App\Http\Requests;

class DynamicZoneTemplateRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zone_id' => 'integer|min:0|max:2147483647|nullable',
            'zone_version' => 'integer|min:-1|max:2147483647|nullable',
            'name' => 'string|max:128|nullable',
            'min_players' => 'integer|min:0|max:2147483647|nullable',
            'max_players' => 'integer|min:0|max:2147483647|nullable',
            'duration_seconds' => 'integer|min:-2147483648|max:2147483647|nullable',
            'dz_switch_id' => 'integer|min:0|max:2147483647|nullable',
            'compass_zone_id' => 'integer|min:0|max:2147483647|nullable',
            'compass_x' => 'numeric|nullable',
            'compass_y' => 'numeric|nullable',
            'compass_z' => 'numeric|nullable',
            'return_zone_id' => 'integer|min:0|max:2147483647|nullable',
            'return_x' => 'numeric|nullable',
            'return_y' => 'numeric|nullable',
            'return_z' => 'numeric|nullable',
            'return_h' => 'numeric|nullable',
            'override_zone_in' => 'integer|min:-128|max:127|nullable',
            'zone_in_x' => 'numeric|nullable',
            'zone_in_y' => 'numeric|nullable',
            'zone_in_z' => 'numeric|nullable',
            'zone_in_h' => 'numeric|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'zone_id' => $this->defaultInt('zone_id', 0),
            'zone_version' => $this->defaultInt('zone_version', 0),
            'name' => $this->defaultString('name', ''),
            'min_players' => $this->defaultInt('min_players', 0),
            'max_players' => $this->defaultInt('max_players', 0),
            'duration_seconds' => $this->defaultInt('duration_seconds', 0),
            'dz_switch_id' => $this->defaultInt('dz_switch_id', 0),
            'compass_zone_id' => $this->defaultInt('compass_zone_id', 0),
            'compass_x' => $this->defaultFloat('compass_x', 0),
            'compass_y' => $this->defaultFloat('compass_y', 0),
            'compass_z' => $this->defaultFloat('compass_z', 0),
            'return_zone_id' => $this->defaultInt('return_zone_id', 0),
            'return_x' => $this->defaultFloat('return_x', 0),
            'return_y' => $this->defaultFloat('return_y', 0),
            'return_z' => $this->defaultFloat('return_z', 0),
            'return_h' => $this->defaultFloat('return_h', 0),
            'override_zone_in' => $this->defaultInt('override_zone_in', 0),
            'zone_in_x' => $this->defaultFloat('zone_in_x', 0),
            'zone_in_y' => $this->defaultFloat('zone_in_y', 0),
            'zone_in_z' => $this->defaultFloat('zone_in_z', 0),
            'zone_in_h' => $this->defaultFloat('zone_in_h', 0),
        ]);
    }
}
