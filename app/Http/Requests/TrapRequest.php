<?php

namespace App\Http\Requests;

class TrapRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'zone' => 'string|max:16|nullable',
            'version' => 'integer|min:0|max:65535|nullable',
            'x' => 'integer|min:-2147483648|max:2147483647|nullable',
            'y' => 'integer|min:-2147483648|max:2147483647|nullable',
            'z' => 'integer|min:-2147483648|max:2147483647|nullable',
            'chance' => 'integer|min:-128|max:127|nullable',
            'maxzdiff' => 'numeric|nullable',
            'radius' => 'numeric|nullable',
            'effect' => 'integer|min:-2147483648|max:2147483647|nullable',
            'effectvalue' => 'integer|min:-2147483648|max:2147483647|nullable',
            'effectvalue2' => 'integer|min:-2147483648|max:2147483647|nullable',
            'message' => 'string|max:200|nullable',
            'skill' => 'integer|min:-2147483648|max:2147483647|nullable',
            'level' => 'integer|min:0|max:16777215|nullable',
            'respawn_time' => 'integer|min:0|max:4294967295|nullable',
            'respawn_var' => 'integer|min:0|max:4294967295|nullable',
            'triggered_number' => 'integer|min:-128|max:127|nullable',
            'group' => 'integer|min:-128|max:127|nullable',
            'despawn_when_triggered' => 'integer|min:-128|max:127|nullable',
            'undetectable' => 'integer|min:-128|max:127|nullable',
            'min_expansion' => 'integer|min:-1|max:127|nullable',
            'max_expansion' => 'integer|min:-1|max:127|nullable',
            'content_flags' => 'string|max:100|nullable',
            'content_flags_disabled' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'zone' => $this->defaultString('zone', ''),
            'version' => $this->defaultInt('version', 0),
            'x' => $this->defaultInt('x', 0),
            'y' => $this->defaultInt('y', 0),
            'z' => $this->defaultInt('z', 0),
            'chance' => $this->defaultInt('chance', 0),
            'maxzdiff' => $this->defaultFloat('maxzdiff', 0),
            'radius' => $this->defaultFloat('radius', 0),
            'effect' => $this->defaultInt('effect', 0),
            'effectvalue' => $this->defaultInt('effectvalue', 0),
            'effectvalue2' => $this->defaultInt('effectvalue2', 0),
            'message' => $this->defaultString('message', ''),
            'skill' => $this->defaultInt('skill', 0),
            'level' => $this->defaultInt('level', 1),
            'respawn_time' => $this->defaultInt('respawn_time', 60),
            'respawn_var' => $this->defaultInt('respawn_var', 0),
            'triggered_number' => $this->defaultInt('triggered_number', 0),
            'group' => $this->defaultInt('group', 0),
            'despawn_when_triggered' => $this->defaultInt('despawn_when_triggered', 0),
            'undetectable' => $this->defaultInt('undetectable', 0),
            'min_expansion' => $this->defaultInt('min_expansion', -1),
            'max_expansion' => $this->defaultInt('max_expansion', -1),
            'content_flags' => $this->defaultString('content_flags', ''),
            'content_flags_disabled' => $this->defaultString('content_flags_disabled', ''),
        ]);
    }
}
