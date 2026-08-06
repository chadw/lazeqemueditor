<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementRequest;
use App\Services\AchievementAggregateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function __construct(private readonly AchievementAggregateService $achievements) {}

    public function index(Request $request): View
    {
        $connection = DB::connection('eqemu');
        $componentCount = $connection->table('achievement_components AS component')
            ->selectRaw('COUNT(*)')
            ->whereColumn('component.achievement_id', 'achievement.id');
        $criteriaCount = $connection->table('achievement_criteria AS criterion')
            ->selectRaw('COUNT(*)')
            ->whereColumn('criterion.achievement_id', 'achievement.id');
        $categoryCount = $connection->table('achievement_category_associations AS association')
            ->selectRaw('COUNT(*)')
            ->whereColumn('association.achievement_id', 'achievement.id');
        $rewardCount = $connection->table('achievement_rewards AS reward')
            ->selectRaw('COUNT(*)')
            ->whereColumn('reward.achievement_id', 'achievement.id');
        $restrictionCount = $connection->table('achievement_cast_restrictions AS restriction')
            ->selectRaw('COUNT(*)')
            ->whereColumn('restriction.achievement_id', 'achievement.id');
        $rewardSetCount = $connection->table('achievement_reward_sets AS reward_set')
            ->selectRaw('COUNT(*)')
            ->whereColumn('reward_set.achievement_id', 'achievement.id');

        $query = $connection->table('achievements AS achievement')
            ->select('achievement.*')
            ->selectSub($componentCount, 'components_count')
            ->selectSub($criteriaCount, 'criteria_count')
            ->selectSub($categoryCount, 'categories_count')
            ->selectSub($rewardCount, 'rewards_count')
            ->selectSub($restrictionCount, 'restrictions_count')
            ->selectSub($rewardSetCount, 'has_reward_set');

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                if (ctype_digit($search)) {
                    $query->where('achievement.id', (int) $search)
                        ->orWhere('achievement.name', 'like', "%{$search}%")
                        ->orWhere('achievement.description', 'like', "%{$search}%");
                } else {
                    $query->where('achievement.name', 'like', "%{$search}%")
                        ->orWhere('achievement.description', 'like', "%{$search}%");
                }
            });
        }

        $enabled = $request->input('enabled');
        if (in_array((string) $enabled, ['0', '1'], true)) {
            $query->where('achievement.enabled', (int) $enabled);
        }

        $categoryId = $request->integer('category_id');
        if ($categoryId > 0) {
            $query->whereExists(function ($query) use ($categoryId): void {
                $query->selectRaw('1')
                    ->from('achievement_category_associations AS selected_association')
                    ->whereColumn('selected_association.achievement_id', 'achievement.id')
                    ->where('selected_association.category_id', $categoryId);
            });
        }

        $eventType = $request->input('event_type');
        if (ctype_digit((string) $eventType) && (int) $eventType >= 0 && (int) $eventType <= 13) {
            $query->whereExists(function ($query) use ($eventType): void {
                $query->selectRaw('1')
                    ->from('achievement_criteria AS filtered_criterion')
                    ->whereColumn('filtered_criterion.achievement_id', 'achievement.id')
                    ->where('filtered_criterion.event_type', (int) $eventType);
            });
        }

        $rewardFilter = (string) $request->input('reward', '');
        if ($rewardFilter === 'any') {
            $query->where(function ($query): void {
                $query->whereExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('achievement_rewards AS filtered_reward')
                        ->whereColumn('filtered_reward.achievement_id', 'achievement.id');
                })->orWhereExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('achievement_reward_sets AS filtered_set')
                        ->whereColumn('filtered_set.achievement_id', 'achievement.id');
                });
            });
        } elseif ($rewardFilter === 'automatic') {
            $query->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('achievement_rewards AS filtered_reward')
                    ->whereColumn('filtered_reward.achievement_id', 'achievement.id')
                    ->whereNotExists(function ($query): void {
                        $query->selectRaw('1')
                            ->from('achievement_reward_option_entries AS filtered_mapping')
                            ->whereColumn('filtered_mapping.reward_id', 'filtered_reward.reward_id');
                    });
            });
        } elseif ($rewardFilter === 'selectable') {
            $query->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('achievement_reward_sets AS filtered_set')
                    ->whereColumn('filtered_set.achievement_id', 'achievement.id');
            });
        } elseif ($rewardFilter === 'none') {
            $query->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('achievement_rewards AS filtered_reward')
                    ->whereColumn('filtered_reward.achievement_id', 'achievement.id');
            })->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('achievement_reward_sets AS filtered_set')
                    ->whereColumn('filtered_set.achievement_id', 'achievement.id');
            });
        }

        $sortable = [
            'id' => 'achievement.id',
            'name' => 'achievement.name',
            'points' => 'achievement.points',
            'definition_version' => 'achievement.definition_version',
            'enabled' => 'achievement.enabled',
            'component_count' => 'components_count',
            'category_count' => 'categories_count',
            'reward_count' => 'rewards_count',
        ];
        $sort = (string) $request->input('sort', 'id');
        if (! array_key_exists($sort, $sortable)) {
            $sort = 'id';
        }
        $direction = strtolower((string) $request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortable[$sort], $direction);
        if ($sort !== 'id') {
            $query->orderBy('achievement.id');
        }

        $perPage = (int) $request->input('per_page', 50);
        if (! in_array($perPage, [25, 50, 100, 200], true)) {
            $perPage = 50;
        }
        $achievementRows = $query->paginate($perPage)->withQueryString();
        $pageIds = $achievementRows->getCollection()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $categoryNames = $pageIds === []
            ? collect()
            : $connection->table('achievement_category_associations AS association')
                ->join('achievement_categories AS category', 'category.id', '=', 'association.category_id')
                ->whereIn('association.achievement_id', $pageIds)
                ->orderBy('association.achievement_id')
                ->orderBy('association.sequence')
                ->orderBy('association.category_id')
                ->get(['association.achievement_id', 'category.name'])
                ->groupBy('achievement_id')
                ->map(fn ($rows) => $rows->pluck('name')->map(fn ($name) => (string) $name)->values()->all());
        $achievementRows->getCollection()->each(function (object $row) use ($categoryNames): void {
            $row->category_names = $categoryNames->get((int) $row->id, []);
            $row->components_count = (int) $row->components_count;
            $row->criteria_count = (int) $row->criteria_count;
            $row->rewards_count = (int) $row->rewards_count;
            $row->has_reward_set = (int) $row->has_reward_set > 0;
        });
        $categoryRows = $this->achievements->categoryRows();

        return view('achievements.index', [
            'achievements' => $achievementRows,
            'categories' => $this->achievements->categoryOptions($categoryRows),
            'metadata' => $this->achievements->metadata(),
            'filters' => [
                'q' => $search,
                'enabled' => $enabled,
                'category_id' => $categoryId ?: null,
                'event_type' => $eventType,
                'reward' => $rewardFilter,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => $perPage,
            ],
            'sortable' => array_keys($sortable),
        ]);
    }

    public function create(): View
    {
        $categoryRows = $this->achievements->categoryRows();

        return view('achievements.create', [
            'editor' => $this->achievements->editorPayload(),
            'categories' => $this->achievements->categoryOptions($categoryRows),
            'metadata' => $this->achievements->metadata(),
        ]);
    }

    public function edit(mixed $achievement): View
    {
        $achievementId = $this->routeId($achievement);
        $categoryRows = $this->achievements->categoryRows();

        return view('achievements.edit', [
            'editor' => $this->achievements->editorPayload($achievementId),
            'categories' => $this->achievements->categoryOptions($categoryRows),
            'metadata' => $this->achievements->metadata(),
        ]);
    }

    public function store(AchievementRequest $request): RedirectResponse
    {
        $achievementId = $this->achievements->store($request->validated());
        toast()->success('Saved!', "Achievement {$achievementId} created.");

        return redirect()->route('achievements.edit', $achievementId);
    }

    public function update(AchievementRequest $request, mixed $achievement): RedirectResponse
    {
        $achievementId = $this->routeId($achievement);
        $this->achievements->update($achievementId, $request->validated());
        toast()->success('Saved!', "Achievement {$achievementId} updated.");

        return redirect()->route('achievements.edit', $achievementId);
    }

    public function clone(mixed $achievement): RedirectResponse
    {
        $newAchievementId = $this->achievements->clone($this->routeId($achievement));
        toast()->success('Cloned!', "Disabled achievement {$newAchievementId} created.");

        return redirect()->route('achievements.edit', $newAchievementId);
    }

    public function destroy(mixed $achievement): RedirectResponse
    {
        $achievementId = $this->routeId($achievement);
        $this->achievements->destroy($achievementId);
        toast()->success(
            'Deleted!',
            "Achievement {$achievementId} content deleted; character history was preserved."
        );

        return redirect()->route('achievements.index');
    }

    private function routeId(mixed $value): int
    {
        if (is_object($value)) {
            if (method_exists($value, 'getKey')) {
                return (int) $value->getKey();
            }
            if (isset($value->id)) {
                return (int) $value->id;
            }
        }

        return (int) $value;
    }
}
