<?php

namespace App\Http\Requests;

class DoorRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'doorid' => 'integer|min:0|max:32767|nullable',
            'zone' => 'string|max:32|nullable',
            'version' => 'integer|min:-1|max:32767|nullable',
            'name' => 'string|max:32|nullable',
            'pos_y' => 'numeric|nullable',
            'pos_x' => 'numeric|nullable',
            'pos_z' => 'numeric|nullable',
            'heading' => 'numeric|nullable',
            'opentype' => 'integer|min:-32768|max:32767|nullable',
            'guild' => 'integer|min:-32768|max:32767|nullable',
            'lockpick' => 'integer|min:-32768|max:32767|nullable',
            'keyitem' => 'integer|min:-2147483648|max:2147483647|nullable',
            'nokeyring' => 'integer|min:0|max:255|nullable',
            'triggerdoor' => 'integer|min:-32768|max:32767|nullable',
            'triggertype' => 'integer|min:-32768|max:32767|nullable',
            'disable_timer' => 'integer|min:-128|max:127|nullable',
            'doorisopen' => 'integer|min:-32768|max:32767|nullable',
            'door_param' => 'integer|min:-2147483648|max:2147483647|nullable',
            'dest_zone' => 'string|max:32|nullable',
            'dest_instance' => 'integer|min:0|max:4294967295|nullable',
            'dest_x' => 'numeric|nullable',
            'dest_y' => 'numeric|nullable',
            'dest_z' => 'numeric|nullable',
            'dest_heading' => 'numeric|nullable',
            'invert_state' => 'integer|min:-2147483648|max:2147483647|nullable',
            'incline' => 'integer|min:-2147483648|max:2147483647|nullable',
            'size' => 'integer|min:0|max:65535|nullable',
            'buffer' => 'numeric|nullable',
            'client_version_mask' => 'integer|min:0|max:4294967295|nullable',
            'is_ldon_door' => 'integer|min:-32768|max:32767|nullable',
            'close_timer_ms' => 'integer|min:0|max:65535|nullable',
            'dz_switch_id' => 'integer|min:-2147483648|max:2147483647|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'doorid' => $this->defaultInt('doorid', 0),
            'zone' => $this->defaultString('zone', ''),
            'version' => $this->defaultInt('version', 0),
            'name' => $this->defaultString('name', ''),
            'pos_y' => $this->defaultFloat('pos_y', 0),
            'pos_x' => $this->defaultFloat('pos_x', 0),
            'pos_z' => $this->defaultFloat('pos_z', 0),
            'heading' => $this->defaultInt('heading', 0),
            'opentype' => $this->defaultInt('opentype', 0),
            'guild' => $this->defaultInt('guild', 0),
            'lockpick' => $this->defaultInt('lockpick', 0),
            'keyitem' => $this->defaultInt('keyitem', 0),
            'nokeyring' => $this->defaultInt('nokeyring', 0),
            'triggerdoor' => $this->defaultInt('triggerdoor', 0),
            'triggertype' => $this->defaultInt('triggertype', 0),
            'disable_timer' => $this->defaultInt('disable_timer', 0),
            'doorisopen' => $this->defaultInt('doorisopen', 0),
            'door_param' => $this->defaultInt('door_param', 0),
            'dest_zone' => $this->defaultString('dest_zone', 'NONE'),
            'dest_instance' => $this->defaultInt('dest_instance', 0),
            'dest_x' => $this->defaultFloat('dest_x', 0),
            'dest_y' => $this->defaultFloat('dest_y', 0),
            'dest_z' => $this->defaultFloat('dest_z', 0),
            'dest_heading' => $this->defaultInt('dest_heading', 0),
            'invert_state' => $this->defaultInt('invert_state', 0),
            'incline' => $this->defaultInt('incline', 0),
            'size' => $this->defaultInt('size', 100),
            'buffer' => $this->defaultInt('buffer', 0),
            'client_version_mask' => $this->defaultInt('client_version_mask', 4294967295),
            'is_ldon_door' => $this->defaultInt('is_ldon_door', 0),
            'close_timer_ms' => $this->defaultInt('close_timer_ms', 5000),
            'dz_switch_id' => $this->defaultInt('dz_switch_id', 0),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
