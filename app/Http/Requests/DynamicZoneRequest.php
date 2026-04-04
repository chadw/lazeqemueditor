<?php

namespace App\Http\Requests;

class DynamicZoneRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'instance_id' => 'integer|min:0|max:2147483647|nullable',
            'type' => 'integer|min:0|max:255|nullable',
            'uuid' => 'required|string|max:36',
            'name' => 'string|max:128|nullable',
            'leader_id' => 'integer|min:0|max:4294967295|nullable',
            'min_players' => 'integer|min:0|max:4294967295|nullable',
            'max_players' => 'integer|min:0|max:4294967295|nullable',
            'dz_switch_id' => 'integer|min:-2147483648|max:2147483647|nullable',
            'compass_zone_id' => 'integer|min:0|max:4294967295|nullable',
            'compass_x' => 'numeric|nullable',
            'compass_y' => 'numeric|nullable',
            'compass_z' => 'numeric|nullable',
            'safe_return_zone_id' => 'integer|min:0|max:4294967295|nullable',
            'safe_return_x' => 'numeric|nullable',
            'safe_return_y' => 'numeric|nullable',
            'safe_return_z' => 'numeric|nullable',
            'safe_return_heading' => 'numeric|nullable',
            'zone_in_x' => 'numeric|nullable',
            'zone_in_y' => 'numeric|nullable',
            'zone_in_z' => 'numeric|nullable',
            'zone_in_heading' => 'numeric|nullable',
            'has_zone_in' => 'integer|min:0|max:255|nullable',
            'is_locked' => 'integer|min:0|max:127|nullable',
            'add_replay' => 'integer|min:0|max:127|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'instance_id' => $this->defaultInt('instance_id', 0),
            'type' => $this->defaultInt('type', 0),
            'uuid' => $this->defaultString('uuid', ''),
            'name' => $this->defaultString('name', ''),
            'leader_id' => $this->defaultInt('leader_id', 0),
            'min_players' => $this->defaultInt('min_players', 0),
            'max_players' => $this->defaultInt('max_players', 0),
            'dz_switch_id' => $this->defaultInt('dz_switch_id', 0),
            'compass_zone_id' => $this->defaultInt('compass_zone_id', 0),
            'compass_x' => $this->defaultFloat('compass_x', 0),
            'compass_y' => $this->defaultFloat('compass_y', 0),
            'compass_z' => $this->defaultFloat('compass_z', 0),
            'safe_return_zone_id' => $this->defaultInt('safe_return_zone_id', 0),
            'safe_return_x' => $this->defaultFloat('safe_return_x', 0),
            'safe_return_y' => $this->defaultFloat('safe_return_y', 0),
            'safe_return_z' => $this->defaultFloat('safe_return_z', 0),
            'safe_return_heading' => $this->defaultFloat('safe_return_heading', 0),
            'zone_in_x' => $this->defaultFloat('zone_in_x', 0),
            'zone_in_y' => $this->defaultFloat('zone_in_y', 0),
            'zone_in_z' => $this->defaultFloat('zone_in_z', 0),
            'zone_in_heading' => $this->defaultFloat('zone_in_heading', 0),
            'has_zone_in' => $this->defaultInt('has_zone_in', 0),
            'is_locked' => $this->defaultInt('is_locked', 0),
            'add_replay' => $this->defaultInt('add_replay', 1),
        ]);
    }
}
