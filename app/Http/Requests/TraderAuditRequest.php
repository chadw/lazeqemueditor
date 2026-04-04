<?php

namespace App\Http\Requests;

class TraderAuditRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'time' => 'date|nullable',
            'seller' => 'string|max:64|nullable',
            'buyer' => 'string|max:64|nullable',
            'itemname' => 'string|max:64|nullable',
            'quantity' => 'integer|min:0|max:2147483647|nullable',
            'totalcost' => 'integer|min:-2147483648|max:2147483647|nullable',
            'trantype' => 'integer|min:-128|max:127|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'time' => $this->defaultString('time', '0000-00-00 00:00:00'),
            'seller' => $this->defaultString('seller', ''),
            'buyer' => $this->defaultString('buyer', ''),
            'itemname' => $this->defaultString('itemname', ''),
            'quantity' => $this->defaultInt('quantity', 0),
            'totalcost' => $this->defaultInt('totalcost', 0),
            'trantype' => $this->defaultInt('trantype', 0),
        ]);
    }
}
