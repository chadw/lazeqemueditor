<?php

namespace App\Http\Requests;

class ZonePointRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zone' => 'string|max:32|nullable',
            'version' => 'integer|min:-1|max:2147483647|nullable',
            'number' => 'integer|min:0|max:65535|nullable',
            'y' => 'numeric|nullable',
            'x' => 'numeric|nullable',
            'z' => 'numeric|nullable',
            'heading' => 'numeric|nullable',
            'target_y' => 'numeric|nullable',
            'target_x' => 'numeric|nullable',
            'target_z' => 'numeric|nullable',
            'target_heading' => 'numeric|nullable',
            'zoneinst' => 'integer|min:0|max:65535|nullable',
            'target_zone_id' => 'integer|min:0|max:4294967295|nullable',
            'target_instance' => 'integer|min:0|max:4294967295|nullable',
            'buffer' => 'numeric|nullable',
            'client_version_mask' => 'integer|min:0|max:4294967295|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
            'is_virtual' => 'integer|min:-128|max:127|nullable',
            'height' => 'integer|min:-2147483648|max:2147483647|nullable',
            'width' => 'integer|min:-2147483648|max:2147483647|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'zone' => $this->defaultString('zone', ''),
            'version' => $this->defaultInt('version', 0),
            'number' => $this->defaultInt('number', 1),
            'y' => $this->defaultFloat('y', 0),
            'x' => $this->defaultFloat('x', 0),
            'z' => $this->defaultFloat('z', 0),
            'heading' => $this->defaultFloat('heading', 0),
            'target_y' => $this->defaultFloat('target_y', 0),
            'target_x' => $this->defaultFloat('target_x', 0),
            'target_z' => $this->defaultFloat('target_z', 0),
            'target_heading' => $this->defaultFloat('target_heading', 0),
            'zoneinst' => $this->defaultInt('zoneinst', 0),
            'target_zone_id' => $this->defaultInt('target_zone_id', 0),
            'target_instance' => $this->defaultInt('target_instance', 0),
            'buffer' => $this->defaultFloat('buffer', 0),
            'client_version_mask' => $this->defaultInt('client_version_mask', 4294967295),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
            'is_virtual' => $this->defaultInt('is_virtual', 0),
            'height' => $this->defaultInt('height', 0),
            'width' => $this->defaultInt('width', 0),
        ]);
    }
}
