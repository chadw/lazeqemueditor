<?php

namespace App\Http\Requests;

class SpawnTwoRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'spawngroupID' => 'integer|min:0|max:2147483647|nullable',
            'zone' => 'string|max:32|nullable',
            'version' => 'integer|min:-1|max:32767|nullable',
            'x' => 'numeric|nullable',
            'y' => 'numeric|nullable',
            'z' => 'numeric|nullable',
            'heading' => 'numeric|nullable',
            'respawntime' => 'integer|min:0|max:2147483647|nullable',
            'variance' => 'integer|min:0|max:2147483647|nullable',
            'pathgrid' => 'integer|min:0|max:2147483647|nullable',
            'path_when_zone_idle' => 'boolean|nullable',
            '_condition' => 'integer|min:0|max:16777215|nullable',
            'cond_value' => 'integer|min:-8388608|max:8388607|nullable',
            'animation' => 'integer|min:0|max:255|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'spawngroupID' => $this->defaultInt('spawngroupID', 0),
            'zone' => $this->defaultString('zone', ''),
            'version' => $this->defaultInt('version', 0),
            'x' => $this->defaultFloat('x', 0.000000),
            'y' => $this->defaultFloat('y', 0.000000),
            'z' => $this->defaultFloat('z', 0.000000),
            'heading' => $this->defaultFloat('heading', 0.000000),
            'respawntime' => $this->defaultInt('respawntime', 0),
            'variance' => $this->defaultInt('variance', 0),
            'pathgrid' => $this->defaultInt('pathgrid', 0),
            'path_when_zone_idle' => $this->defaultInt('path_when_zone_idle', 0),
            '_condition' => $this->defaultInt('_condition', 0),
            'cond_value' => $this->defaultInt('cond_value', 1),
            'animation' => $this->defaultInt('animation', 0),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
