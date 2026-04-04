<?php

namespace App\Http\Requests;

class SpawnGroupRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:200|nullable',
            'spawn_limit' => 'integer|min:-128|max:127|nullable',
            'dist' => 'numeric|nullable',
            'max_x' => 'numeric|nullable',
            'min_x' => 'numeric|nullable',
            'max_y' => 'numeric|nullable',
            'min_y' => 'numeric|nullable',
            'delay' => 'integer|min:-2147483648|max:2147483647|nullable',
            'mindelay' => 'integer|min:-2147483648|max:2147483647|nullable',
            'despawn' => 'integer|min:-128|max:127|nullable',
            'despawn_timer' => 'integer|min:-2147483648|max:2147483647|nullable',
            'wp_spawns' => 'boolean|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'spawn_limit' => $this->defaultInt('spawn_limit', 0),
            'dist' => $this->defaultFloat('dist', 0),
            'max_x' => $this->defaultFloat('max_x', 0),
            'min_x' => $this->defaultFloat('min_x', 0),
            'max_y' => $this->defaultFloat('max_y', 0),
            'min_y' => $this->defaultFloat('min_y', 0),
            'delay' => $this->defaultInt('delay', 45000),
            'mindelay' => $this->defaultInt('mindelay', 15000),
            'despawn' => $this->defaultInt('despawn', 0),
            'despawn_timer' => $this->defaultInt('despawn_timer', 100),
            'wp_spawns' => $this->defaultInt('wp_spawns', 0),
        ]);
    }
}
