<?php

namespace App\Http\Requests;

class TaskActivityRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'taskid' => 'integer|nullable',
            'activityid' => 'integer|nullable',
            'req_activity_id' => 'integer|nullable',
            'step' => 'integer|nullable',
            'activitytype' => 'integer|nullable',
            'target_name' => 'string|max:64|nullable',
            'goalmethod' => 'integer|nullable',
            'goalcount' => 'integer|nullable',
            'description_override' => 'string|max:128|nullable',
            'npc_match_list' => 'array|nullable',
            'npc_match_list.*' => 'numeric',
            'item_id_list' => 'array|nullable',
            'item_id_list.*' => 'numeric',
            'item_list' => 'string|max:128|nullable',
            'dz_switch_id' => 'integer|nullable',
            'min_x' => 'numeric|nullable',
            'min_y' => 'numeric|nullable',
            'min_z' => 'numeric|nullable',
            'max_x' => 'numeric|nullable',
            'max_y' => 'numeric|nullable',
            'max_z' => 'numeric|nullable',
            'skill_list' => 'string|max:64|nullable',
            'spell_list' => 'string|max:64|nullable',
            'zones' => 'array|nullable',
            'zones.*' => 'numeric',
            'zone_version' => 'integer|nullable',
            'optional' => 'integer|nullable',
            'list_group' => 'integer|nullable',
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

        if (isset($validated['npc_match_list']) && is_array($validated['npc_match_list'])) {
            $validated['npc_match_list'] = implode('|', $validated['npc_match_list']);
        }

        if (isset($validated['item_id_list']) && is_array($validated['item_id_list'])) {
            $validated['item_id_list'] = implode('|', $validated['item_id_list']);
        }

        if (isset($validated['zones']) && is_array($validated['zones'])) {
            $validated['zones'] = implode(';', $validated['zones']);
        }

        return $validated;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'step' => $this->defaultInt('step', 0),
            'description_override' => (string) $this->input('description_override', ''),
            'npc_match_list' => $this->input('npc_match_list', null),
            'item_id_list' => $this->input('item_id_list', null),
            'item_list' => (string) $this->input('item_list', ''),
            'list_group' => $this->defaultInt('list_group', 0),
            'zone_version' => $this->defaultInt('zone_version', -1),
            'min_x' => $this->defaultInt('min_x', 0),
            'min_y' => $this->defaultInt('min_y', 0),
            'min_z' => $this->defaultInt('min_z', 0),
            'max_x' => $this->defaultInt('max_x', 0),
            'max_y' => $this->defaultInt('max_y', 0),
            'max_z' => $this->defaultInt('max_z', 0),
        ]);

        // Normalize list inputs so validator accepts arrays when client sends '0' or pipe/comma strings
        $npc = $this->input('npc_match_list');
        if (!is_array($npc)) {
            if (is_null($npc) || $npc === '' || $npc === '0') {
                $npcArr = [];
            } elseif (is_string($npc)) {
                if (strpos($npc, '|') !== false) {
                    $npcArr = array_values(array_filter(explode('|', $npc), fn($v) => $v !== ''));
                } elseif (strpos($npc, ',') !== false) {
                    $npcArr = array_values(array_filter(explode(',', $npc), fn($v) => $v !== ''));
                } else {
                    $npcArr = [$npc];
                }
            } else {
                $npcArr = [];
            }
            $this->merge(['npc_match_list' => $npcArr]);
        }

        // Normalize zones input so validator accepts arrays when client sends '0' or semicolon-separated strings
        $zones = $this->input('zones');
        if (!is_array($zones)) {
            if (is_null($zones) || $zones === '' || $zones === '0') {
                $zonesArr = [];
            } elseif (is_string($zones)) {
                if (strpos($zones, ';') !== false) {
                    $zonesArr = array_values(array_filter(explode(';', $zones), fn($v) => $v !== ''));
                } else {
                    $zonesArr = [$zones];
                }
            } else {
                $zonesArr = [];
            }
            $this->merge(['zones' => $zonesArr]);
        }

        $items = $this->input('item_id_list');
        if (!is_array($items)) {
            if (is_null($items) || $items === '' || $items === '0') {
                $itemsArr = [];
            } elseif (is_string($items)) {
                if (strpos($items, '|') !== false) {
                    $itemsArr = array_values(array_filter(explode('|', $items), fn($v) => $v !== ''));
                } elseif (strpos($items, ',') !== false) {
                    $itemsArr = array_values(array_filter(explode(',', $items), fn($v) => $v !== ''));
                } else {
                    $itemsArr = [$items];
                }
            } else {
                $itemsArr = [];
            }
            $this->merge(['item_id_list' => $itemsArr]);
        }
    }
}
