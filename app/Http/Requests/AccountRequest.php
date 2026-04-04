<?php

namespace App\Http\Requests;

class AccountRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string|max:30|nullable',
            'charname' => 'string|max:64|nullable',
            'auto_login_charname' => 'string|max:64|nullable',
            'sharedplat' => 'integer|min:0|max:4294967295|nullable',
            'password' => 'string|max:50|nullable',
            'status' => 'integer|min:-2|max:255|nullable',
            'ls_id' => 'string|max:64|nullable',
            'lsaccount_id' => 'integer|min:0|max:4294967295|nullable',
            'gmspeed' => 'integer|min:0|max:255|nullable',
            'invulnerable' => 'integer|min:0|max:127|nullable',
            'flymode' => 'integer|min:0|max:127|nullable',
            'ignore_tells' => 'integer|min:0|max:127|nullable',
            'revoked' => 'integer|min:0|max:255|nullable',
            'karma' => 'integer|min:0|max:4294967295|nullable',
            'minilogin_ip' => 'string|max:32|nullable',
            'hideme' => 'integer|min:0|max:127|nullable',
            'rulesflag' => 'boolean|nullable',
            'suspendeduntil' => 'date|nullable',
            'time_creation' => 'integer|min:0|max:4294967295|nullable',
            'ban_reason' => 'string|nullable',
            'suspend_reason' => 'string|nullable',
            'crc_eqgame' => 'string|nullable',
            'crc_skillcaps' => 'string|nullable',
            'crc_basedata' => 'string|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        // not all fields editable, so commented them out for now.

        $this->merge([
            //'name' => $this->defaultString('name', ''),
            //'charname' => $this->defaultString('charname', ''),
            //'auto_login_charname' => $this->defaultString('auto_login_charname', ''),
            'sharedplat' => $this->defaultInt('sharedplat', 0),
            //'password' => $this->defaultString('password', ''),
            'status' => $this->defaultInt('status', 0),
            //'ls_id' => $this->defaultString('ls_id', 'eqemu'),
            //'lsaccount_id' => $this->defaultInt('lsaccount_id', 0),
            'gmspeed' => $this->defaultInt('gmspeed', 0),
            'invulnerable' => $this->defaultInt('invulnerable', 0),
            'flymode' => $this->defaultInt('flymode', 0),
            'ignore_tells' => $this->defaultInt('ignore_tells', 0),
            'revoked' => $this->defaultInt('revoked', 0),
            //'karma' => $this->defaultInt('karma', 0),
            //'minilogin_ip' => $this->defaultString('minilogin_ip', ''),
            'hideme' => $this->defaultInt('hideme', 0),
            //'rulesflag' => $this->defaultInt('rulesflag', 0),
            'suspendeduntil' => $this->filled('suspendeduntil') ? $this->input('suspendeduntil') : null,
            //'suspendeduntil' => $this->defaultString('suspendeduntil', ''),
            //'time_creation' => $this->defaultInt('time_creation', 0),
            'ban_reason' => $this->defaultString('ban_reason', ''),
            'suspend_reason' => $this->defaultString('suspend_reason', ''),
            //'crc_eqgame' => $this->defaultString('crc_eqgame', ''),
            //'crc_skillcaps' => $this->defaultString('crc_skillcaps', ''),
            //'crc_basedata' => $this->defaultString('crc_basedata', ''),
        ]);
    }
}
