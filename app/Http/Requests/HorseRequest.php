<?php

namespace App\Http\Requests;

class HorseRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'filename' => 'string|max:32|nullable',
            'race' => 'integer|min:0|max:32767|nullable',
            'gender' => 'integer|min:0|max:2|nullable',
            'texture' => 'integer|min:0|max:127|nullable',
            'helmtexture' => 'integer|min:-1|max:127|nullable',
            'mountspeed' => 'numeric|nullable',
            'notes' => 'string|max:64|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'filename' => $this->defaultString('filename', ''),
            'race' => $this->defaultInt('race', 216),
            'gender' => $this->defaultInt('gender', 0),
            'texture' => $this->defaultInt('texture', 0),
            'helmtexture' => $this->defaultInt('helmtexture', -1),
            'mountspeed' => $this->defaultFloat('mountspeed', 0.75),
            'notes' => $this->defaultString('notes', 'None'),
        ]);
    }
}
