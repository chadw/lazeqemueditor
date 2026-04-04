<?php

namespace App\Http\Requests;

class GroundSpawnRequest extends BaseRequest
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
            'max_x' => 'numeric|nullable',
            'max_y' => 'numeric|nullable',
            'max_z' => 'numeric|nullable',
            'min_x' => 'numeric|nullable',
            'min_y' => 'numeric|nullable',
            'heading' => 'numeric|nullable',
            'name' => 'string|max:16|nullable',
            'item' => 'integer|min:0|max:4294967295|nullable',
            'max_allowed' => 'integer|min:0|max:4294967295|nullable',
            'comment' => 'string|max:255|nullable',
            'respawn_timer' => 'integer|min:0|max:4294967295|nullable',
            'fix_z' => 'boolean|nullable',
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
            'max_x' => $this->defaultFloat('max_x', 2000),
            'max_y' => $this->defaultFloat('max_y', 2000),
            'max_z' => $this->defaultFloat('max_z', 10000),
            'min_x' => $this->defaultFloat('min_x', -2000),
            'min_y' => $this->defaultFloat('min_y', -2000),
            'heading' => $this->defaultInt('heading', 0),
            'name' => $this->defaultString('name', ''),
            'item' => $this->defaultInt('item', 0),
            'max_allowed' => $this->defaultInt('max_allowed', 1),
            'comment' => $this->defaultString('comment', ''),
            'respawn_timer' => $this->defaultInt('respawn_timer', 300),
            'fix_z' => $this->defaultInt('fix_z', 1),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
