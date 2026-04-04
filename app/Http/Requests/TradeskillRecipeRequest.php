<?php

namespace App\Http\Requests;

class TradeskillRecipeRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:64|nullable',
            'tradeskill' => 'integer|min:0|max:32767|nullable',
            'skillneeded' => 'integer|min:0|max:32767|nullable',
            'trivial' => 'integer|min:0|max:32767|nullable',
            'nofail' => 'boolean|nullable',
            'replace_container' => 'boolean|nullable',
            'notes' => 'string|nullable',
            'must_learn' => 'integer|min:0|max:127|nullable',
            'learned_by_item_id' => 'integer|min:0|max:2147483647|nullable',
            'quest' => 'boolean|nullable',
            'enabled' => 'boolean|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
            'l_method'  => ['required', 'in:0,1,2'],
            'l_message' => ['required', 'in:0,16'],
            'l_search'  => ['required', 'in:0,32'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'tradeskill' => $this->defaultInt('tradeskill', 0),
            'skillneeded' => $this->defaultInt('skillneeded', 0),
            'trivial' => $this->defaultInt('trivial', 0),
            'nofail' => $this->defaultInt('nofail', 0),
            'replace_container' => $this->defaultInt('replace_container', 0),
            'notes' => $this->defaultString('notes', ''),
            'must_learn' => $this->defaultInt('must_learn', 0),
            'learned_by_item_id' => $this->defaultInt('learned_by_item_id', 0),
            'quest' => $this->defaultInt('quest', 0),
            'enabled' => $this->defaultInt('enabled', 1),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
