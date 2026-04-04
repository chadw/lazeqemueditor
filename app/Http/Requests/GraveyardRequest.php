<?php

namespace App\Http\Requests;

class GraveyardRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zone_id' => 'integer|nullable',
            'x' => 'numeric|nullable',
            'y' => 'numeric|nullable',
            'z' => 'numeric|nullable',
            'heading' => 'numeric|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'x'       => $this->defaultFloat('x', 0),
            'y'       => $this->defaultFloat('y', 0),
            'z'       => $this->defaultFloat('z', 0),
            'heading' => $this->defaultFloat('heading', 0),
        ]);
    }
}
