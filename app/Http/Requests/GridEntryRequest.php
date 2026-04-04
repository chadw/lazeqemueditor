<?php

namespace App\Http\Requests;

class GridEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'gridid' => 'integer|min:0|max:2147483647|nullable',
            'zoneid' => 'integer|min:1|max:2147483647|nullable',
            'number' => 'integer|min:0|max:2147483647|nullable',
            'x' => 'numeric|nullable',
            'y' => 'numeric|nullable',
            'z' => 'numeric|nullable',
            'heading' => 'numeric|nullable',
            'pause' => 'integer|min:0|max:2147483647|nullable',
            'centerpoint' => 'integer|min:0|max:127|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'gridid' => $this->defaultInt('gridid', 0),
            'zoneid' => $this->defaultInt('zoneid', 0),
            'number' => $this->defaultInt('number', 0),
            'x' => $this->defaultFloat('x', 0),
            'y' => $this->defaultFloat('y', 0),
            'z' => $this->defaultFloat('z', 0),
            'heading' => $this->defaultFloat('heading', 0),
            'pause' => $this->defaultInt('pause', 0),
            'centerpoint' => $this->defaultInt('centerpoint', 0),
        ]);
    }
}
