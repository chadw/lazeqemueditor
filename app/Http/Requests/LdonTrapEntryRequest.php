<?php

namespace App\Http\Requests;

class LdonTrapEntryRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'trap_id' => 'integer|min:0|max:4294967295|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'trap_id' => $this->defaultInt('trap_id', 0),
        ]);
    }
}
