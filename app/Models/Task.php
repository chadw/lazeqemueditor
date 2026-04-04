<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'tasks';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'duration',
        'duration_code',
        'title',
        'description',
        'reward_text',
        'reward_id_list',
        'cash_reward',
        'exp_reward',
        'reward_method',
        'reward_points',
        'reward_point_type',
        'min_level',
        'max_level',
        'level_spread',
        'min_players',
        'max_players',
        'repeatable',
        'faction_reward',
        'completion_emote',
        'replay_timer_group',
        'replay_timer_seconds',
        'request_timer_group',
        'request_timer_seconds',
        'dz_template_id',
        'lock_activity_id',
        'faction_amount',
        'enabled',
    ];

    protected $casts = [
        'repeatable' => 'boolean',
        'enabled' => 'boolean',
    ];

    public function taskActivities(): HasMany
    {
        return $this->hasMany(TaskActivity::class, 'taskid', 'id')
            ->orderBy('activityid', 'asc')
            ->orderBy('step', 'asc');
    }

    public function faction(): BelongsTo
    {
        return $this->belongsTo(FactionList::class, 'faction_reward');
    }

    public function getTaskTypeAttribute(): string
    {
        return match ($this->type) {
            0 => 'Task',
            1 => 'Shared',
            2 => 'Quest',
            3 => 'Expedition',
            default => 'Unknown',
        };
    }

    public function getFixedDescriptionAttribute()
    {
        $desc = $this->description ?? '';
        if (!$desc) return '';

        $regex = '/\[(.*?)\]/';

        $result = [
            'global' => $this->replaceDescriptionContent(trim(preg_replace($regex, '', $desc))),
            'activities' => []
        ];

        if (preg_match_all($regex, $desc, $matches)) {
            foreach ($matches[1] as $match) {
                $desc_split = array_map('trim', explode(',', $match));
                $steps = [];

                // desc can be associated with multiple steps
                while (!empty($desc_split) && is_numeric($desc_split[0])) {
                    // pluck the step
                    $steps[] = (int) array_shift($desc_split);
                }

                if ($steps && $desc_split) {
                    $description = $this->replaceDescriptionContent(implode(',', $desc_split));

                    $result['activities'][] = [
                        'steps' => $steps,
                        'text' => $description,
                    ];
                }
            }
        }

        return $result;
    }

    private function replaceDescriptionContent($desc): string
    {
        $desc = trim($desc);
        $desc = str_replace(']', '', $desc);
        $desc = str_ireplace('<br>', '<br />', $desc);
        $desc = preg_replace_callback('/<c\s+"(#[0-9A-Fa-f]{3,6})">/', function ($matches) {
            return '<span style="color: ' . $matches[1] . '">';
        }, $desc);
        $desc = str_ireplace('</c>', '</span>', $desc);

        return $desc;
    }

    public static function attachRewardsMultiple(Collection|Paginator $tasks): Collection|Paginator
    {
        $arePaginated = $tasks instanceof Paginator;

        $allTasks = $arePaginated ? $tasks->getCollection() : $tasks;

        $rewardIds = $allTasks->flatMap(function ($task) {
            return explode('|', $task->reward_id_list);
        })->unique()->filter();

        $items = Item::whereIn('id', $rewardIds)->select('id', 'Name', 'icon')->get()->keyBy('id');

        $allTasks->each(function ($task) use ($items) {
            $ids = array_map('intval', explode('|', $task->reward_id_list));
            $task->rewards = collect($ids)
                ->map(fn($id) => $items[$id] ?? null)
                ->filter();
        });

        if ($arePaginated) {
            $tasks->setCollection($allTasks);

            return $tasks;
        }

        return $allTasks;
    }

    public static function attachRewardsSingle(Task $task): Task
    {
        $rewardIds = array_map('intval', explode('|', $task->reward_id_list));
        $items = Item::whereIn('id', $rewardIds)->select('id', 'Name', 'icon')->get()->keyBy('id');

        $task->rewards = collect($rewardIds)
            ->map(fn($id) => $items[$id] ?? null)
            ->filter();

        return $task;
    }
}
