<?php

namespace App\Http\Requests;

class TaskRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'integer|nullable',
            'duration' => 'integer|nullable',
            'duration_code' => 'integer|nullable',
            'title' => 'string|max:100|nullable',
            'description' => 'required|string',
            'reward_text' => 'string|max:64|nullable',
            'reward_id_list' => 'array|nullable',
            'reward_id_list.*' => 'numeric',
            'cash_reward' => 'integer|nullable',
            'exp_reward' => 'integer|nullable',
            'reward_method' => 'integer|nullable',
            'reward_points' => 'integer|nullable',
            'reward_point_type' => 'integer|nullable',
            'min_level' => 'integer|nullable',
            'max_level' => 'integer|nullable',
            'level_spread' => 'integer|nullable',
            'min_players' => 'integer|nullable',
            'max_players' => 'integer|nullable',
            'repeatable' => 'integer|nullable',
            'faction_reward' => 'integer|nullable',
            'completion_emote' => 'string|max:512|nullable',
            'replay_timer_group' => 'integer|nullable',
            'replay_timer_seconds' => 'integer|nullable',
            'request_timer_group' => 'integer|nullable',
            'request_timer_seconds' => 'integer|nullable',
            'dz_template_id' => 'integer|nullable',
            'lock_activity_id' => 'integer|nullable',
            'faction_amount' => 'integer|nullable',
            'enabled' => 'integer|nullable',
        ];
    }

    /**
     * Force the array into a pipe string AFTER validation passes.
     *
     * @param  mixed $key
     * @param  mixed $default
     * @return void
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        if (isset($validated['reward_id_list']) && is_array($validated['reward_id_list'])) {
            $validated['reward_id_list'] = implode('|', $validated['reward_id_list']);
        }

        return $validated;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'duration' => $this->defaultInt('duration', 0),
            'min_level' => $this->defaultInt('min_level', 0),
            'max_level' => $this->defaultInt('max_level', 0),
            'level_spread' => $this->defaultInt('level_spread', 0),
            'min_players' => $this->defaultInt('min_players', 0),
            'max_players' => $this->defaultInt('max_players', 0),
            'dz_template_id' => $this->defaultInt('dz_template_id', 0),
            'faction_reward' => $this->defaultInt('faction_reward', 0),
            'completion_emote' => (string) $this->input('completion_emote', ''),
            'lock_activity_id' => $this->defaultInt('lock_activity_id', -1),
            'request_timer_group' => $this->defaultInt('request_timer_group', 0),
            'request_timer_seconds' => $this->defaultInt('request_timer_seconds', 0),
            'replay_timer_group' => $this->defaultInt('replay_timer_group', 0),
            'replay_timer_seconds' => $this->defaultInt('replay_timer_seconds', 0),
            'reward_text' => (string) $this->input('reward_text', ''),
            'cash_reward' => $this->defaultInt('cash_reward', 0),
            'exp_reward' => $this->defaultInt('exp_reward', 0),
            'reward_points' => $this->defaultInt('reward_points', 0),
            'faction_amount' => $this->defaultInt('faction_amount', 0),
        ]);
    }
}
