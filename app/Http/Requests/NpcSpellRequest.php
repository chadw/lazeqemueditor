<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class NpcSpellRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'npc_id' => [
                'nullable',
                'integer',
                Rule::exists('eqemu.npc_types', 'id'),
            ],
            'name' => 'string|nullable',
            'parent_list' => 'integer|min:0|max:4294967295|nullable',
            'attack_proc' => 'integer|min:-1|max:32767|nullable',
            'proc_chance' => 'integer|min:0|max:100|nullable',
            'range_proc' => 'integer|min:-1|max:32767|nullable',
            'rproc_chance' => 'integer|min:0|max:100|nullable',
            'defensive_proc' => 'integer|min:-1|max:32767|nullable',
            'dproc_chance' => 'integer|min:0|max:100|nullable',
            'fail_recast' => 'integer|min:0|max:4294967295|nullable',
            'engaged_no_sp_recast_min' => 'integer|min:0|max:4294967295|nullable',
            'engaged_no_sp_recast_max' => 'integer|min:0|max:4294967295|nullable',
            'engaged_b_self_chance' => 'integer|min:0|max:255|nullable',
            'engaged_b_other_chance' => 'integer|min:0|max:255|nullable',
            'engaged_d_chance' => 'integer|min:0|max:255|nullable',
            'pursue_no_sp_recast_min' => 'integer|min:0|max:4294967295|nullable',
            'pursue_no_sp_recast_max' => 'integer|min:0|max:4294967295|nullable',
            'pursue_d_chance' => 'integer|min:0|max:255|nullable',
            'idle_no_sp_recast_min' => 'integer|min:0|max:4294967295|nullable',
            'idle_no_sp_recast_max' => 'integer|min:0|max:4294967295|nullable',
            'idle_b_chance' => 'integer|min:0|max:255|nullable',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->defaultString('name', ''),
            'parent_list' => $this->defaultInt('parent_list', 0),
            'attack_proc' => $this->defaultInt('attack_proc', -1),
            'proc_chance' => $this->defaultInt('proc_chance', 3),
            'range_proc' => $this->defaultInt('range_proc', -1),
            'rproc_chance' => $this->defaultInt('rproc_chance', 0),
            'defensive_proc' => $this->defaultInt('defensive_proc', -1),
            'dproc_chance' => $this->defaultInt('dproc_chance', 0),
            'fail_recast' => $this->defaultInt('fail_recast', 0),
            'engaged_no_sp_recast_min' => $this->defaultInt('engaged_no_sp_recast_min', 0),
            'engaged_no_sp_recast_max' => $this->defaultInt('engaged_no_sp_recast_max', 0),
            'engaged_b_self_chance' => $this->defaultInt('engaged_b_self_chance', 0),
            'engaged_b_other_chance' => $this->defaultInt('engaged_b_other_chance', 0),
            'engaged_d_chance' => $this->defaultInt('engaged_d_chance', 0),
            'pursue_no_sp_recast_min' => $this->defaultInt('pursue_no_sp_recast_min', 0),
            'pursue_no_sp_recast_max' => $this->defaultInt('pursue_no_sp_recast_max', 0),
            'pursue_d_chance' => $this->defaultInt('pursue_d_chance', 0),
            'idle_no_sp_recast_min' => $this->defaultInt('idle_no_sp_recast_min', 0),
            'idle_no_sp_recast_max' => $this->defaultInt('idle_no_sp_recast_max', 0),
            'idle_b_chance' => $this->defaultInt('idle_b_chance', 0),
        ]);
    }
}
