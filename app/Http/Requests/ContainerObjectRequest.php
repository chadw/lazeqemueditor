<?php

namespace App\Http\Requests;

class ContainerObjectRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zoneid' => 'integer|min:0|max:4294967295|nullable',
            'version' => 'integer|min:-1|max:32767|nullable',
            'xpos' => 'numeric|nullable',
            'ypos' => 'numeric|nullable',
            'zpos' => 'numeric|nullable',
            'heading' => 'numeric|nullable',
            'itemid' => 'integer|min:-2147483648|max:2147483647|nullable',
            'charges' => 'integer|min:0|max:65535|nullable',
            'objectname' => 'string|max:32|nullable',
            'type' => 'integer|min:-2147483648|max:2147483647|nullable',
            'icon' => 'integer|min:-2147483648|max:2147483647|nullable',
            'size_percentage' => 'numeric|nullable',
            'unknown24' => 'integer|min:-2147483648|max:2147483647|nullable',
            'unknown60' => 'integer|min:-2147483648|max:2147483647|nullable',
            'unknown64' => 'integer|min:-2147483648|max:2147483647|nullable',
            'unknown68' => 'integer|min:-2147483648|max:2147483647|nullable',
            'unknown72' => 'integer|min:-2147483648|max:2147483647|nullable',
            'unknown76' => 'integer|min:-2147483648|max:2147483647|nullable',
            'unknown84' => 'integer|min:-2147483648|max:2147483647|nullable',
            'size' => 'numeric|nullable',
            'solid_type' => 'integer|min:-8388608|max:8388607|nullable',
            'incline' => 'integer|min:-2147483648|max:2147483647|nullable',
            'tilt_x' => 'numeric|nullable',
            'tilt_y' => 'numeric|nullable',
            'display_name' => 'string|max:64|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'zoneid' => $this->defaultInt('zoneid', 0),
            'version' => $this->defaultInt('version', 0),
            'xpos' => $this->defaultFloat('xpos', 0),
            'ypos' => $this->defaultFloat('ypos', 0),
            'zpos' => $this->defaultFloat('zpos', 0),
            'heading' => $this->defaultFloat('heading', 0),
            'itemid' => $this->defaultInt('itemid', 0),
            'charges' => $this->defaultInt('charges', 0),
            'objectname' => $this->defaultString('objectname', ''),
            'type' => $this->defaultInt('type', 0),
            'icon' => $this->defaultInt('icon', 0),
            'size_percentage' => $this->defaultFloat('size_percentage', 0),
            'unknown24' => $this->defaultInt('unknown24', 0),
            'unknown60' => $this->defaultInt('unknown60', 0),
            'unknown64' => $this->defaultInt('unknown64', 0),
            'unknown68' => $this->defaultInt('unknown68', 0),
            'unknown72' => $this->defaultInt('unknown72', 0),
            'unknown76' => $this->defaultInt('unknown76', 0),
            'unknown84' => $this->defaultInt('unknown84', 0),
            'size' => $this->defaultFloat('size', 100),
            'solid_type' => $this->defaultInt('solid_type', 0),
            'incline' => $this->defaultInt('incline', 0),
            'tilt_x' => $this->defaultFloat('tilt_x', 0),
            'tilt_y' => $this->defaultFloat('tilt_y', 0),
            'display_name' => $this->defaultString('display_name', ''),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
