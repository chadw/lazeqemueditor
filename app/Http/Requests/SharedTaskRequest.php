<?php

namespace App\Http\Requests;

class SharedTaskRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'task_id' => 'integer|nullable',
            'accepted_time' => 'date|nullable',
            'expire_time' => 'date|nullable',
            'completion_time' => 'date|nullable',
            'is_locked' => 'integer|nullable',
        ];
    }
}
