<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Task;
use App\Models\Zone;
use App\Models\NpcType;
use App\Filters\TaskFilter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CharacterTask;
use App\Models\CompletedTask;
use App\Models\AlternateCurrency;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\DB;


class TaskController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['id', 'title', 'task_activities_count', 'enabled'];
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');
        if (!in_array($sort, $sortable)) {
            $sort = 'id';
        }

        $query = (new TaskFilter($request))
            ->apply(Task::query())
            ->withCount('taskActivities');

        if ($sort === 'enabled') {
            $query->orderBy('enabled', $direction)
                ->orderBy('id', 'asc');
        } else {
            $query->orderBy($sort, $direction);
        }

        $tasks = Task::attachRewardsMultiple(
            $query->paginate(50)->withQueryString()
        );

        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    public function active(Request $request)
    {
        $sortable = ['charid', 'type', 'task', 'character'];
        $sort = $request->input('sort', 'charid');
        $direction = $request->input('direction', 'asc');
        if (! in_array($sort, $sortable)) {
            $sort = 'charid';
        }

        $query = CharacterTask::query()
            ->with(['task', 'character']);

        if ($sort === 'task') {
            $query->leftJoin('tasks', 'tasks.id', '=', 'character_tasks.taskid')
                ->orderBy('tasks.title', $direction);
        }
        elseif ($sort === 'character') {
            $query->leftJoin('character_data', 'character_data.id', '=', 'character_tasks.charid')
                ->orderBy('character_data.name', $direction);
        }
        else {
            $query->orderBy('character_tasks.charid', $direction);
        }

        $tasks = $query
            ->select('character_tasks.*')
            ->paginate(50)
            ->withQueryString();

        return view('tasks.active', compact('tasks'));
    }

    public function completed(Request $request)
    {
        $tasks = CompletedTask::with([
            'task',
            'character',
            //'activity',
        ])
        ->paginate(50)
        ->withQueryString();

        return view('tasks.completed', compact('tasks'));
    }

    public function edit(Task $task)
    {
        $task->attachRewardsSingle($task);
        $task->load([
            'faction',
            'taskActivities.zone',
        ]);

        $npcIds = $this->collectPipeIds($task->taskActivities, 'npc_match_list');
        $itemIds = $this->collectPipeIds($task->taskActivities, 'item_id_list');
        $zoneIds = $this->collectPipeIds($task->taskActivities, 'zones');

        $npcs = $npcIds ? NpcType::whereIn('id', $npcIds)->get(['id', 'name'])->keyBy('id') : collect();
        $items = $itemIds ? Item::whereIn('id', $itemIds)->get(['id', 'Name', 'icon'])->keyBy('id') : collect();
        $zones = $zoneIds ? Zone::whereIn('zoneidnumber', $zoneIds)
            ->get(['zoneidnumber', 'short_name', 'long_name'])
            ->keyBy('zoneidnumber') : collect();

        $task->taskActivities->each(function($activity) use ($npcs, $items, $zones) {
            $activity->setRelation('npcs', $npcs->only(explode('|', $activity->npc_match_list)));
            $activity->setRelation('items', $items->only(explode('|', $activity->item_id_list)));
            $zoneSep = strpos($activity->zones ?? '', ';') !== false ? ';' : '|';
            $activity->setRelation('zones', $zones->only(explode($zoneSep, $activity->zones)));
        });

        $altCurrency = AlternateCurrency::allAltCurrency()
            ->mapWithKeys(fn ($c) => [
                $c->id => [
                    'name' => $c->item->Name ?? 'Unknown',
                    'icon' => $c->item->icon ?? '',
                ],
            ]);

        return view('tasks.edit', compact('task', 'altCurrency'));
    }

    public function store(TaskRequest $request)
    {
        $task = Task::create($request->validated());

        return redirect()
            ->route('tasks.edit', $task)
            ->with('success', 'Task created');
    }

    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()
            ->route('tasks.edit', $task)
            ->with('success', 'Task updated');
    }

    public function destroy(Task $task)
    {
        DB::connection('eqemu')->transaction(function () use ($task) {
            // delete all activities for this task first
            DB::connection('eqemu')->table('task_activities')
                ->where('taskid', $task->id)
                ->delete();

            // then delete the task itself
            $task->delete();
        });

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted');
    }

    public function clone(Request $request, Task $task)
    {
        $new = $task->replicate();

        $suffix = ' (Copy)';
        $newTitle = $task->title . $suffix;
        if (Task::where('title', $newTitle)->exists()) {
            $newTitle = $task->title . $suffix . ' ' . now()->format('YmdHis');
        }
        $new->title = $newTitle;

        $newId = null;

        DB::connection('eqemu')->transaction(function () use (&$new, &$newId, $task) {
            $table = $new->getTable();
            $max = DB::connection('eqemu')->table($table)->lockForUpdate()->max('id');
            $newId = (($max ?? 0) + 1);
            $new->id = $newId;
            $new->save();

            $activities = $task->taskActivities()->get();
            foreach ($activities as $act) {
                $insert = $act->getAttributes();
                $insert['taskid'] = $newId;

                DB::connection('eqemu')->table('task_activities')->insert($insert);
            }
        });

        return redirect()
            ->route('tasks.edit', ['task' => $newId ?? $new->id])
            ->with('success', 'Task cloned');
    }

    public function deleteActive($taskid, $charid)
    {
        CharacterTask::where('taskid', $taskid)
            ->where('charid', $charid)
            ->delete();

        return back()->with('success', 'Active Task deleted.');
    }

    public function deleteCompleted($taskid, $charid)
    {
        CompletedTask::where('taskid', $taskid)
            ->where('charid', $charid)
            ->delete();

        return back()->with('success', 'Completed Task removed.');
    }

    public function timerGroupsDetail(Request $request)
    {
        $field = $request->input('field', 'replay');
        $col = $field === 'request' ? 'request_timer_group' : 'replay_timer_group';

        $rows = Task::select("{$col} as grp", 'id', 'title', 'description')
            ->whereNotNull($col)
            ->where($col, '!=', 0)
            ->orderBy('id')
            ->get();

        $groups = [];
        $missingIds = [];

        foreach ($rows as $r) {
            $g = (int) $r->grp;
            if (!isset($groups[$g])) {
                $groups[$g] = [
                    'id' => $g,
                    'tasks' => []
                ];
            }

            $title = trim((string) ($r->title ?? ''));
            if ($title === '') {
                $desc = trim((string) ($r->description ?? ''));
                $desc = strip_tags($desc);
                if ($desc !== '') {
                    $title = Str::limit(preg_replace('/\s+/', ' ', $desc), 80);
                } else {
                    $missingIds[] = $r->id;
                    $title = null;
                }
            }

            $raw = $r->title ?? '';
            $groups[$g]['tasks'][] = [
                'title' => $title,
                'id' => $r->id,
            ];
        }

        if (!empty($missingIds)) {
            $titles = Task::whereIn('id', $missingIds)
                ->pluck('title', 'id')
                ->toArray();

            foreach ($groups as &$grp) {
                foreach ($grp['tasks'] as &$t) {
                    if (empty($t['title']) && isset($titles[$t['id']]) && trim((string)$titles[$t['id']]) !== '') {
                        $t['title'] = trim((string)$titles[$t['id']]);
                    }
                    if (empty($t['title'])) {
                        $t['title'] = 'Task #' . ($t['id'] ?? '?');
                    }
                }
                unset($t);
            }
            unset($grp);
        }

        if ($groups) ksort($groups, SORT_NUMERIC);
        $out = array_values($groups);

        return response()->json($out);
    }

    private function collectPipeIds($activities, string $field): array
    {
        if (!$activities) return [];

        $coll = collect($activities)->flatMap(function ($act) use ($field) {
            $raw = (string) ($act->{$field} ?? '');
            $sep = $field === 'zones' ? ';' : '|';
            return explode($sep, $raw);
        })->filter()->map(fn($v) => (int) $v)->unique()->values();

        return $coll->all();
    }
}
