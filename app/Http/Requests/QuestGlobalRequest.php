<?php

namespace App\Http\Requests;

class QuestGlobalRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'old_name' => 'string|nullable',
            'old_charid' => 'integer|nullable',
            'old_npcid' => 'integer|nullable',
            'old_zoneid' => 'integer|nullable',
            'charid' => 'integer|nullable',
            'npcid' => 'integer|nullable',
            'zoneid' => 'integer|nullable',
            'name' => 'string|max:65|nullable',
            'value' => 'string|max:128|nullable',
            'expdate' => 'nullable',
        ];
    }
}
