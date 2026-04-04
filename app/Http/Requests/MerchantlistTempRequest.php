<?php

namespace App\Http\Requests;

class MerchantlistTempRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npcid' => 'integer|nullable',
            'slot' => 'integer|nullable',
            'zone_id' => 'integer|nullable',
            'instance_id' => 'integer|nullable',
            'itemid' => 'integer|nullable',
            'charges' => 'integer|nullable',
        ];
    }
}
