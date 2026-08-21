<?php

namespace App\Services;

use App\Support\Achievements\AchievementMetadata;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AchievementAggregateService
{
    private const UINT32_MAX = 4294967295;

    private const REWARD_SOURCE_ACHIEVEMENT = 1;

    /** @var list<string> */
    private const CHARACTER_ACHIEVEMENT_STATE_TABLES = [
        'character_achievements',
        'character_achievement_progress',
        'character_achievement_rewards',
        'character_achievement_reward_selections',
        'character_achievement_pending_updates',
    ];

    public function metadata(): array
    {
        return [
            'component_types' => AchievementMetadata::COMPONENT_TYPES,
            'event_types' => AchievementMetadata::EVENTS,
            'progress_modes' => AchievementMetadata::PROGRESS_MODES,
            'allowed_progress_modes' => AchievementMetadata::ALLOWED_PROGRESS_MODES,
            'behaviors' => AchievementMetadata::BEHAVIORS,
            'reward_types' => AchievementMetadata::REWARD_TYPES,
            'target_guidance' => AchievementMetadata::TARGET_GUIDANCE,
            'classes' => [
                0 => 'Any class',
                1 => 'Warrior',
                2 => 'Cleric',
                3 => 'Paladin',
                4 => 'Ranger',
                5 => 'Shadow Knight',
                6 => 'Druid',
                7 => 'Monk',
                8 => 'Bard',
                9 => 'Rogue',
                10 => 'Shaman',
                11 => 'Necromancer',
                12 => 'Wizard',
                13 => 'Magician',
                14 => 'Enchanter',
                15 => 'Beastlord',
                16 => 'Berserker',
            ],
            'limits' => [
                'uint32_max' => self::UINT32_MAX,
                'highest_skill_id' => 77,
                'skill_wildcard_id' => self::UINT32_MAX,
                'highest_class_id' => 16,
                'highest_skill_cap_level' => 255,
            ],
        ];
    }

    public function editorPayload(?int $achievementId = null): array
    {
        $connection = DB::connection('eqemu');
        $suggestedAchievementId = $achievementId === null
            ? $this->suggestedAchievementId($connection)
            : $this->suggestedId($connection, 'achievements', 'id');
        $suggestedComponentId = $this->suggestedId(
            $connection,
            'achievement_associations',
            'component_id'
        );
        $suggestedRewardSetId = $this->suggestedId($connection, 'reward_sets', 'reward_set_id');

        if ($achievementId === null) {
            return [
                'id' => $suggestedAchievementId,
                'name' => '',
                'description' => '',
                'icon_id' => 0,
                'points' => 0,
                'has_reward' => 0,
                'client_flag' => 0,
                'version' => 0,
                'reset_on_version_change' => 1,
                'enabled' => 0,
                'associations' => [],
                'components' => [],
                'rewards' => [],
                'reward_set' => null,
                'restrictions' => [],
                'suggested_achievement_id' => $suggestedAchievementId,
                'suggested_component_id' => $suggestedComponentId,
                'suggested_reward_set_id' => $suggestedRewardSetId,
                'is_create' => true,
            ];
        }

        $definition = $connection->table('achievements')->where('id', $achievementId)->first();
        if (! $definition) {
            $this->notFound('Achievement', $achievementId);
        }

        $associations = $connection->table('achievement_category_associations')
            ->where('achievement_id', $achievementId)
            ->orderBy('sequence')
            ->orderBy('category_id')
            ->get()
            ->map(fn (object $row) => [
                'category_id' => (int) $row->category_id,
                'sequence' => (int) $row->sequence,
                'display_text' => (string) $row->display_text,
            ])
            ->all();

        $criteriaByComponent = [];
        $criteria = $connection->table('achievement_criteria')
            ->where('achievement_id', $achievementId)
            ->orderBy('component_type')
            ->orderBy('component_sequence')
            ->orderBy('id')
            ->get();
        foreach ($criteria as $criterion) {
            $key = $this->componentKey($criterion->component_type, $criterion->component_id);
            $criteriaByComponent[$key][] = [
                'id' => (string) $criterion->id,
                'event_type' => (int) $criterion->event_type,
                'progress_mode' => (int) $criterion->progress_mode,
                'behavior' => (int) $criterion->behavior,
                'target_id' => (int) $criterion->target_id,
                'target_id2' => (int) $criterion->target_id2,
                // Keep BIGINT values as decimal strings so JSON/Alpine cannot
                // round them through JavaScript's 53-bit Number range.
                'target_value' => (string) $criterion->target_value,
                'required_count' => (int) $criterion->required_count,
                'enabled' => (int) $criterion->enabled,
            ];
        }

        $components = $connection->table('achievement_components AS component')
            ->leftJoin(
                'achievement_associations AS count',
                'count.component_id',
                '=',
                'component.component_id'
            )
            ->where('component.achievement_id', $achievementId)
            ->orderBy('component.component_type')
            ->orderBy('component.sequence')
            ->orderBy('component.component_id')
            ->get([
                'component.component_type',
                'component.sequence',
                'component.component_id',
                'component.name',
                'component.description',
                'count.required_count AS presentation_count',
            ])
            ->map(function (object $row) use ($criteriaByComponent) {
                $key = $this->componentKey($row->component_type, $row->component_id);

                return [
                    'component_type' => (int) $row->component_type,
                    'sequence' => (int) $row->sequence,
                    'component_id' => (int) $row->component_id,
                    'name' => (string) $row->name,
                    'description' => (string) $row->description,
                    'presentation_count' => max(1, (int) ($row->presentation_count ?? 1)),
                    'criteria' => $criteriaByComponent[$key] ?? [],
                ];
            })
            ->all();

        $automaticRewards = $connection->table('reward_source_entries AS entry')
            ->join('rewards AS reward', 'reward.reward_id', '=', 'entry.reward_id')
            ->where('entry.source_type', self::REWARD_SOURCE_ACHIEVEMENT)
            ->where('entry.source_id', $achievementId)
            ->orderBy('entry.sequence')
            ->orderBy('entry.reward_id')
            ->get([
                'reward.reward_id',
                'entry.sequence',
                'reward.reward_type',
                'reward.reward_data_id',
                'reward.amount',
                'reward.description',
                'reward.enabled',
            ])
            ->map(fn (object $row) => $this->rewardPayloadRow($row, null));

        $rewardSource = $connection->table('reward_sources AS source')
            ->join('reward_sets AS reward_set', 'reward_set.reward_set_id', '=', 'source.reward_set_id')
            ->where('source.source_type', self::REWARD_SOURCE_ACHIEVEMENT)
            ->where('source.source_id', $achievementId)
            ->first([
                'source.reward_set_id',
                'source.enabled AS source_enabled',
                'reward_set.title',
                'reward_set.enabled',
            ]);
        $rewardSet = null;
        $selectableRewards = collect();
        if ($rewardSource) {
            $rewardSetId = (int) $rewardSource->reward_set_id;
            $rewardSet = [
                'reward_set_id' => $rewardSetId,
                'title' => (string) $rewardSource->title,
                'enabled' => (int) $rewardSource->enabled,
                'source_enabled' => (int) $rewardSource->source_enabled,
                'options' => $connection->table('reward_options')
                    ->where('reward_set_id', $rewardSetId)
                    ->orderBy('sequence')
                    ->orderBy('option_id')
                    ->get()
                    ->map(fn (object $row) => [
                        'option_id' => (int) $row->option_id,
                        'sequence' => (int) $row->sequence,
                        'label' => (string) $row->label,
                        'common_to_all' => (int) $row->common_to_all,
                        'flags' => (int) $row->flags,
                        'enabled' => (int) $row->enabled,
                    ])
                    ->all(),
            ];

            $selectableRewards = $connection->table('reward_option_entries AS entry')
                ->join('rewards AS reward', 'reward.reward_id', '=', 'entry.reward_id')
                ->where('entry.reward_set_id', $rewardSetId)
                ->orderBy('entry.option_id')
                ->orderBy('entry.sequence')
                ->orderBy('entry.reward_id')
                ->get([
                    'reward.reward_id',
                    'entry.sequence',
                    'entry.option_id',
                    'reward.reward_type',
                    'reward.reward_data_id',
                    'reward.amount',
                    'reward.description',
                    'reward.enabled',
                ])
                ->map(fn (object $row) => $this->rewardPayloadRow($row, (int) $row->option_id));
        }

        $rewards = $automaticRewards->concat($selectableRewards)->values()->all();

        $restrictions = $connection->table('achievement_cast_requirements')
            ->where('achievement_id', $achievementId)
            ->orderBy('restriction_id')
            ->get()
            ->map(fn (object $row) => [
                'restriction_id' => (int) $row->restriction_id,
                'requires_completed' => (int) $row->requires_completed,
            ])
            ->all();

        return [
            'id' => (int) $definition->id,
            'name' => (string) $definition->name,
            'description' => (string) $definition->description,
            'icon_id' => (int) $definition->icon_id,
            'points' => (int) $definition->points,
            'has_reward' => (int) $definition->has_reward,
            'client_flag' => (int) $definition->client_flag,
            'version' => (int) $definition->version,
            'reset_on_version_change' => (int) $definition->reset_on_version_change,
            'enabled' => (int) $definition->enabled,
            'associations' => $associations,
            'components' => $components,
            'rewards' => $rewards,
            'reward_set' => $rewardSet,
            'restrictions' => $restrictions,
            'suggested_achievement_id' => $suggestedAchievementId,
            'suggested_component_id' => $suggestedComponentId,
            'suggested_reward_set_id' => $rewardSet['reward_set_id'] ?? $suggestedRewardSetId,
            'is_create' => false,
        ];
    }

    public function categoryRows(): array
    {
        $connection = DB::connection('eqemu');
        $associationCount = $connection->table('achievement_category_associations AS association')
            ->selectRaw('COUNT(*)')
            ->whereColumn('association.category_id', 'category.id');
        $childrenCount = $connection->table('achievement_categories AS child')
            ->selectRaw('COUNT(*)')
            ->whereColumn('child.parent_id', 'category.id');

        $rows = $connection->table('achievement_categories AS category')
            ->select('category.*')
            ->selectSub($associationCount, 'associations_count')
            ->selectSub($childrenCount, 'children_count')
            ->orderBy('category.parent_id')
            ->orderBy('category.sequence')
            ->orderBy('category.id')
            ->get()
            ->map(fn (object $row) => [
                'id' => (int) $row->id,
                'parent_id' => (int) $row->parent_id,
                'sequence' => (int) $row->sequence,
                'name' => (string) $row->name,
                'description' => (string) $row->description,
                'icon' => (string) $row->icon,
                'associations_count' => (int) $row->associations_count,
                'children_count' => (int) $row->children_count,
            ])
            ->all();

        $children = [];
        foreach ($rows as $row) {
            $children[$row['parent_id']][] = $row;
        }

        $ordered = [];
        $visited = [];
        $walk = function (int $parentId, int $depth) use (&$walk, &$ordered, &$visited, $children): void {
            foreach ($children[$parentId] ?? [] as $row) {
                if (isset($visited[$row['id']])) {
                    continue;
                }
                $visited[$row['id']] = true;
                $row['depth'] = $depth;
                $ordered[] = $row;
                $walk($row['id'], $depth + 1);
            }
        };

        $walk(0, 0);
        // Keep malformed/orphaned rows visible so an administrator can repair
        // them without allowing a cycle to recurse indefinitely.
        foreach ($rows as $row) {
            if (isset($visited[$row['id']])) {
                continue;
            }
            $visited[$row['id']] = true;
            $row['depth'] = 0;
            $ordered[] = $row;
            $walk($row['id'], 1);
        }

        return $ordered;
    }

    public function categoryOptions(?array $categoryRows = null): array
    {
        $categoryRows ??= $this->categoryRows();
        $options = [];
        foreach ($categoryRows as $row) {
            $options[(int) $row['id']] = str_repeat('— ', (int) $row['depth']).$row['name'];
        }

        return $options;
    }

    public function store(array $data): int
    {
        $connection = DB::connection('eqemu');

        return $connection->transaction(function () use ($connection, $data): int {
            $achievementId = (int) $data['id'];
            if ($connection->table('achievements')->where('id', $achievementId)->lockForUpdate()->exists()) {
                $this->fail('id', "Achievement {$achievementId} already exists.");
            }
            if ($this->achievementIdHasCharacterState($connection, $achievementId, true)) {
                $this->fail(
                    'id',
                    "Achievement {$achievementId} has preserved character state and cannot be reused. Choose a new stable ID."
                );
            }

            $this->assertAggregateReferences($connection, $data, $achievementId);
            $connection->table('achievements')->insert($this->achievementRow($data));
            $this->syncAggregate($connection, $achievementId, $data);

            return $achievementId;
        });
    }

    public function update(int $achievementId, array $data): void
    {
        $connection = DB::connection('eqemu');
        $connection->transaction(function () use ($connection, $achievementId, $data): void {
            $existing = $connection->table('achievements')
                ->where('id', $achievementId)
                ->lockForUpdate()
                ->first();
            if (! $existing) {
                $this->notFound('Achievement', $achievementId);
            }
            if ((int) $data['id'] !== $achievementId) {
                $this->fail('id', 'The stable achievement ID cannot be changed after creation.');
            }

            $this->assertAggregateReferences($connection, $data, $achievementId);
            $connection->table('achievements')
                ->where('id', $achievementId)
                ->update($this->achievementRow($data, false));
            $this->syncAggregate($connection, $achievementId, $data);
        });
    }

    public function clone(int $achievementId): int
    {
        $connection = DB::connection('eqemu');

        return $connection->transaction(function () use ($connection, $achievementId): int {
            $source = $connection->table('achievements')
                ->where('id', $achievementId)
                ->lockForUpdate()
                ->first();
            if (! $source) {
                $this->notFound('Achievement', $achievementId);
            }

            $data = $this->editorPayload($achievementId);
            $newAchievementId = $this->allocateAchievementId($connection);
            $data['id'] = $newAchievementId;
            $data['name'] = Str::limit((string) $data['name'], 248, '').' (Copy)';
            $data['version'] = 0;
            $data['enabled'] = 0;
            $data['restrictions'] = [];
            foreach ($data['rewards'] as &$reward) {
                $reward['reward_id'] = null;
            }
            unset($reward);
            if (is_array($data['reward_set'])) {
                $data['reward_set']['reward_set_id'] = null;
            }

            $this->assertAggregateReferences($connection, $data, $newAchievementId);
            $connection->table('achievements')->insert($this->achievementRow($data));
            $this->syncAggregate($connection, $newAchievementId, $data);

            return $newAchievementId;
        });
    }

    public function destroy(int $achievementId): void
    {
        $connection = DB::connection('eqemu');
        $connection->transaction(function () use ($connection, $achievementId): void {
            $definition = $connection->table('achievements')
                ->where('id', $achievementId)
                ->lockForUpdate()
                ->first();
            if (! $definition) {
                $this->notFound('Achievement', $achievementId);
            }

            $incomingDependency = $connection->table('achievement_criteria')
                ->where('event_type', 11)
                ->where('target_id', $achievementId)
                ->where('achievement_id', '!=', $achievementId)
                ->exists();
            if ($incomingDependency) {
                $this->fail(
                    'achievement',
                    'This achievement is referenced by another achievement criterion. Remove that dependency first.'
                );
            }

            // The shared reward catalog is intentionally retained. Removing
            // this achievement only detaches its provider mappings; reward
            // definitions and sets may be reused by tasks or other sources.
            $connection->table('reward_source_entries')
                ->where('source_type', self::REWARD_SOURCE_ACHIEVEMENT)
                ->where('source_id', $achievementId)
                ->delete();
            $connection->table('reward_sources')
                ->where('source_type', self::REWARD_SOURCE_ACHIEVEMENT)
                ->where('source_id', $achievementId)
                ->delete();
            $connection->table('achievement_cast_requirements')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_criteria')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_components')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_category_associations')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievements')->where('id', $achievementId)->delete();

            // Character completion/progress/reward ledgers and the global
            // component association table are intentionally retained as history.
        });
    }

    public function storeCategory(array $data): int
    {
        $connection = DB::connection('eqemu');

        return $connection->transaction(function () use ($connection, $data): int {
            $categoryId = (int) $data['id'];
            if ($connection->table('achievement_categories')->where('id', $categoryId)->lockForUpdate()->exists()) {
                $this->fail('id', "Achievement category {$categoryId} already exists.");
            }
            $this->assertCategoryParent($connection, $categoryId, (int) $data['parent_id']);
            $connection->table('achievement_categories')->insert($this->categoryRow($data));

            return $categoryId;
        });
    }

    public function updateCategory(int $categoryId, array $data): void
    {
        $connection = DB::connection('eqemu');
        $connection->transaction(function () use ($connection, $categoryId, $data): void {
            $category = $connection->table('achievement_categories')
                ->where('id', $categoryId)
                ->lockForUpdate()
                ->first();
            if (! $category) {
                $this->notFound('AchievementCategory', $categoryId);
            }
            if ((int) $data['id'] !== $categoryId) {
                $this->fail('id', 'The stable category ID cannot be changed after creation.');
            }

            $this->assertCategoryParent($connection, $categoryId, (int) $data['parent_id']);
            $connection->table('achievement_categories')
                ->where('id', $categoryId)
                ->update($this->categoryRow($data, false));
        });
    }

    public function destroyCategory(int $categoryId): void
    {
        $connection = DB::connection('eqemu');
        $connection->transaction(function () use ($connection, $categoryId): void {
            $category = $connection->table('achievement_categories')
                ->where('id', $categoryId)
                ->lockForUpdate()
                ->first();
            if (! $category) {
                $this->notFound('AchievementCategory', $categoryId);
            }
            if ($connection->table('achievement_categories')->where('parent_id', $categoryId)->exists()) {
                $this->fail('category', 'Move or delete this category’s children before deleting it.');
            }
            if ($connection->table('achievement_category_associations')->where('category_id', $categoryId)->exists()) {
                $this->fail('category', 'Remove this category’s achievement associations before deleting it.');
            }

            $connection->table('achievement_categories')->where('id', $categoryId)->delete();
        });
    }

    private function syncAggregate(ConnectionInterface $connection, int $achievementId, array $data): void
    {
        $this->syncAssociations($connection, $achievementId, $data['associations'] ?? []);
        $this->syncComponents($connection, $achievementId, $data['components'] ?? []);
        $this->syncRewards(
            $connection,
            $achievementId,
            $data['rewards'] ?? [],
            $data['reward_set'] ?? null
        );
        $this->syncRestrictions($connection, $achievementId, $data['restrictions'] ?? []);
    }

    private function syncAssociations(ConnectionInterface $connection, int $achievementId, array $associations): void
    {
        $connection->table('achievement_category_associations')
            ->where('achievement_id', $achievementId)
            ->delete();

        $rows = [];
        foreach ($associations as $association) {
            $rows[] = [
                'category_id' => (int) $association['category_id'],
                'sequence' => (int) $association['sequence'],
                'achievement_id' => $achievementId,
                'display_text' => (string) ($association['display_text'] ?? ''),
            ];
        }
        if ($rows !== []) {
            $connection->table('achievement_category_associations')->insert($rows);
        }
    }

    private function syncComponents(ConnectionInterface $connection, int $achievementId, array $components): void
    {
        foreach ($components as $index => $component) {
            $componentId = (int) $component['component_id'];
            $submittedCount = (int) $component['presentation_count'];
            $referencedByAnotherAchievement = $connection->table('achievement_components')
                ->where('component_id', $componentId)
                ->where('achievement_id', '!=', $achievementId)
                ->lockForUpdate()
                ->exists();
            if (! $referencedByAnotherAchievement) {
                continue;
            }

            $storedCount = $connection->table('achievement_associations')
                ->where('component_id', $componentId)
                ->lockForUpdate()
                ->value('required_count');
            $effectiveStoredCount = max(1, (int) ($storedCount ?? 1));
            if ($effectiveStoredCount !== $submittedCount) {
                $this->fail(
                    "components.{$index}.presentation_count",
                    "Component ID {$componentId} is shared by another achievement and its global presentation count is {$effectiveStoredCount}."
                );
            }
        }

        $connection->table('achievement_criteria')->where('achievement_id', $achievementId)->delete();
        $connection->table('achievement_components')->where('achievement_id', $achievementId)->delete();

        $componentRows = [];
        $criterionRows = [];
        foreach ($components as $component) {
            $componentType = (int) $component['component_type'];
            $componentId = (int) $component['component_id'];
            $sequence = (int) $component['sequence'];
            $componentRows[] = [
                'achievement_id' => $achievementId,
                'component_type' => $componentType,
                'sequence' => $sequence,
                'component_id' => $componentId,
                'name' => (string) ($component['name'] ?? ''),
                'description' => (string) ($component['description'] ?? ''),
            ];

            // component_id is a deliberately global presentation identity.
            // Never delete its count merely because this aggregate stopped
            // using it; edit/create only upserts the submitted value.
            $connection->table('achievement_associations')->updateOrInsert(
                ['component_id' => $componentId],
                ['required_count' => (int) $component['presentation_count']]
            );

            foreach (($component['criteria'] ?? []) as $criterion) {
                $criterionRows[] = [
                    'achievement_id' => $achievementId,
                    'component_type' => $componentType,
                    'component_sequence' => $sequence,
                    'component_id' => $componentId,
                    'event_type' => (int) $criterion['event_type'],
                    'progress_mode' => (int) $criterion['progress_mode'],
                    'behavior' => (int) $criterion['behavior'],
                    'target_id' => (int) $criterion['target_id'],
                    'target_id2' => (int) $criterion['target_id2'],
                    'target_value' => (int) $criterion['target_value'],
                    'required_count' => (int) $criterion['required_count'],
                    'enabled' => (int) $criterion['enabled'],
                ];
            }
        }

        if ($componentRows !== []) {
            $connection->table('achievement_components')->insert($componentRows);
        }
        if ($criterionRows !== []) {
            $connection->table('achievement_criteria')->insert($criterionRows);
        }
    }

    private function syncRewards(
        ConnectionInterface $connection,
        int $achievementId,
        array $rewards,
        ?array $rewardSet
    ): void {
        $existingSource = $connection->table('reward_sources')
            ->where('source_type', self::REWARD_SOURCE_ACHIEVEMENT)
            ->where('source_id', $achievementId)
            ->lockForUpdate()
            ->first();
        $existingSetId = $existingSource ? (int) $existingSource->reward_set_id : null;
        $existingRewardIds = $this->existingGraphRewardIds(
            $connection,
            $achievementId,
            $existingSetId
        );
        $existingRewardIdMap = array_fill_keys($existingRewardIds, true);
        foreach ($rewards as $index => $reward) {
            if (($reward['reward_id'] ?? null) === null) {
                continue;
            }
            if (! isset($existingRewardIdMap[(string) $reward['reward_id']])) {
                $this->fail(
                    "rewards.{$index}.reward_id",
                    'Existing reward IDs are immutable and cannot be adopted from outside this achievement source graph.'
                );
            }
        }

        $existingSetShared = $existingSetId !== null
            && $connection->table('reward_sources')
                ->where('reward_set_id', $existingSetId)
                ->where(function ($query) use ($achievementId): void {
                    $query->where('source_type', '!=', self::REWARD_SOURCE_ACHIEVEMENT)
                        ->orWhere('source_id', '!=', $achievementId);
                })
                ->lockForUpdate()
                ->exists();
        $rewardSetId = null;
        if ($rewardSet !== null) {
            $requestedSetId = $rewardSet['reward_set_id'] ?? null;
            if ($existingSource) {
                if ($requestedSetId === null) {
                    $this->fail(
                        'reward_set.reward_set_id',
                        'A selectable reward source requires a stable reward-set ID.'
                    );
                }
                $rewardSetId = (int) $requestedSetId;
                if ($rewardSetId !== $existingSetId) {
                    if (! $existingSetShared) {
                        $this->fail(
                            'reward_set.reward_set_id',
                            'The stable reward-set ID cannot be changed after creation unless the current set is shared and must be forked.'
                        );
                    }
                    if (
                        $connection->table('reward_sets')
                            ->where('reward_set_id', $rewardSetId)
                            ->lockForUpdate()
                            ->exists()
                    ) {
                        $this->fail(
                            'reward_set.reward_set_id',
                            'Choose an unused reward-set ID when forking a shared set.'
                        );
                    }
                }
            } elseif ($requestedSetId !== null) {
                $rewardSetId = (int) $requestedSetId;
                if (
                    $connection->table('reward_sets')
                        ->where('reward_set_id', $rewardSetId)
                        ->lockForUpdate()
                        ->exists()
                ) {
                    $this->fail('reward_set.reward_set_id', 'That reward-set ID is already in use.');
                }
            } else {
                $rewardSetId = $this->allocateId(
                    $connection,
                    'reward_sets',
                    'reward_set_id',
                    'reward_set.reward_set_id'
                );
            }
        }

        $sharedSet = $existingSetShared && $rewardSetId === $existingSetId;
        if ($sharedSet && $rewardSet !== null) {
            $this->assertSharedRewardSetUnchanged($connection, $existingSetId, $rewardSet, $rewards);
        }

        $connection->table('reward_source_entries')
            ->where('source_type', self::REWARD_SOURCE_ACHIEVEMENT)
            ->where('source_id', $achievementId)
            ->delete();

        $savedRewards = [];
        foreach ($rewards as $index => $reward) {
            $row = $this->rewardCanonicalRow($reward);

            if (($reward['reward_id'] ?? null) !== null) {
                $rewardId = (int) $reward['reward_id'];
                $stored = $connection->table('rewards')
                    ->where('reward_id', $rewardId)
                    ->lockForUpdate()
                    ->first();
                if (! $stored) {
                    $this->fail("rewards.{$index}.reward_id", 'The canonical reward row no longer exists.');
                }
                $sharedReward = ($existingSetShared && ($reward['option_id'] ?? null) !== null)
                    || $this->rewardIsReferencedOutsideGraph(
                        $connection,
                        $rewardId,
                        $achievementId,
                        $existingSetId
                    );
                if ($sharedReward && ! $this->rewardRowMatches($stored, $row)) {
                    $this->fail(
                        "rewards.{$index}",
                        'This canonical reward is shared by another source. Detach or clone it before changing the grant definition.'
                    );
                }
                if (! $sharedReward) {
                    $connection->table('rewards')->where('reward_id', $rewardId)->update($row);
                }
            } else {
                if ($sharedSet && ($reward['option_id'] ?? null) !== null) {
                    $this->fail(
                        "rewards.{$index}",
                        'A shared reward set cannot receive a new grant from one source. Use a new reward-set ID first.'
                    );
                }
                $rewardId = (int) $connection->table('rewards')
                    ->insertGetId($row, 'reward_id');
                if ($rewardId < 1 || $rewardId > self::UINT32_MAX) {
                    $this->fail(
                        "rewards.{$index}.reward_id",
                        'The next automatic reward ID does not fit the RoF2 unsigned 32-bit field.'
                    );
                }
            }
            $savedRewards[] = [
                'reward_id' => $rewardId,
                'sequence' => (int) $reward['sequence'],
                'option_id' => $reward['option_id'] ?? null,
            ];
        }

        $automaticRows = [];
        foreach ($savedRewards as $index => $reward) {
            if ($reward['option_id'] !== null) {
                continue;
            }
            $automaticRows[] = [
                'source_type' => self::REWARD_SOURCE_ACHIEVEMENT,
                'source_id' => $achievementId,
                'sequence' => $reward['sequence'],
                'reward_id' => $reward['reward_id'],
            ];
        }
        if ($automaticRows !== []) {
            $connection->table('reward_source_entries')->insert($automaticRows);
        }

        if ($rewardSet === null) {
            foreach ($savedRewards as $index => $reward) {
                if ($reward['option_id'] !== null) {
                    $this->fail("rewards.{$index}.option_id", 'A reward mapping requires a reward set.');
                }
            }

            $connection->table('reward_sources')
                ->where('source_type', self::REWARD_SOURCE_ACHIEVEMENT)
                ->where('source_id', $achievementId)
                ->delete();

            return;
        }

        $connection->table('reward_sources')->updateOrInsert([
            'source_type' => self::REWARD_SOURCE_ACHIEVEMENT,
            'source_id' => $achievementId,
        ], [
            'reward_set_id' => $rewardSetId,
            'enabled' => (int) ($rewardSet['source_enabled'] ?? 1),
        ]);

        if ($sharedSet) {
            return;
        }

        $connection->table('reward_sets')->updateOrInsert([
            'reward_set_id' => $rewardSetId,
        ], [
            'title' => (string) ($rewardSet['title'] ?? ''),
            'enabled' => (int) $rewardSet['enabled'],
        ]);

        $connection->table('reward_option_entries')->where('reward_set_id', $rewardSetId)->delete();
        $connection->table('reward_options')->where('reward_set_id', $rewardSetId)->delete();

        $optionRows = [];
        $optionIds = [];
        foreach (($rewardSet['options'] ?? []) as $option) {
            $optionId = (int) $option['option_id'];
            $optionIds[$optionId] = true;
            $optionRows[] = [
                'reward_set_id' => $rewardSetId,
                'option_id' => $optionId,
                'sequence' => (int) $option['sequence'],
                'label' => (string) ($option['label'] ?? ''),
                'common_to_all' => (int) $option['common_to_all'],
                'flags' => (int) $option['flags'],
                'enabled' => (int) $option['enabled'],
            ];
        }
        if ($optionRows !== []) {
            $connection->table('reward_options')->insert($optionRows);
        }

        $mappingRows = [];
        foreach ($savedRewards as $index => $reward) {
            if ($reward['option_id'] === null) {
                continue;
            }
            $optionId = (int) $reward['option_id'];
            if (! isset($optionIds[$optionId])) {
                $this->fail("rewards.{$index}.option_id", 'The mapped reward option does not exist.');
            }
            $mappingRows[] = [
                'reward_set_id' => $rewardSetId,
                'option_id' => $optionId,
                'sequence' => $reward['sequence'],
                'reward_id' => $reward['reward_id'],
            ];
        }
        if ($mappingRows !== []) {
            $connection->table('reward_option_entries')->insert($mappingRows);
        }
    }

    private function syncRestrictions(ConnectionInterface $connection, int $achievementId, array $restrictions): void
    {
        $connection->table('achievement_cast_requirements')
            ->where('achievement_id', $achievementId)
            ->delete();

        $rows = [];
        foreach ($restrictions as $restriction) {
            $rows[] = [
                'restriction_id' => (int) $restriction['restriction_id'],
                'achievement_id' => $achievementId,
                'requires_completed' => (int) $restriction['requires_completed'],
            ];
        }
        if ($rows !== []) {
            $connection->table('achievement_cast_requirements')->insert($rows);
        }
    }

    private function assertAggregateReferences(
        ConnectionInterface $connection,
        array $data,
        int $achievementId
    ): void {
        $categoryParents = $connection->table('achievement_categories')
            ->lockForUpdate()
            ->pluck('parent_id', 'id')
            ->mapWithKeys(fn ($parentId, $id) => [(int) $id => (int) $parentId])
            ->all();
        foreach (($data['associations'] ?? []) as $index => $association) {
            $categoryId = (int) $association['category_id'];
            if (! array_key_exists($categoryId, $categoryParents)) {
                $this->fail("associations.{$index}.category_id", 'The selected achievement category does not exist.');
            }
            $this->assertValidCategoryLineage($categoryParents, $categoryId, "associations.{$index}.category_id");
        }

        $dependencyTargets = [];
        foreach (($data['components'] ?? []) as $component) {
            foreach (($component['criteria'] ?? []) as $criterion) {
                if ((int) $criterion['event_type'] !== 11 || (int) $criterion['target_id'] === 0) {
                    continue;
                }
                $targetId = (int) $criterion['target_id'];
                if ($targetId === $achievementId && (int) $criterion['enabled'] === 1) {
                    $this->fail('components', 'An enabled achievement cannot depend on its own completion.');
                }
                $dependencyTargets[$targetId] = true;
            }
        }

        if ($dependencyTargets !== []) {
            $existingTargets = $connection->table('achievements')
                ->whereIn('id', array_keys($dependencyTargets))
                ->lockForUpdate()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
            $missingTargets = array_diff(array_keys($dependencyTargets), $existingTargets, [$achievementId]);
            if ($missingTargets !== []) {
                $this->fail(
                    'components',
                    'Achievement-complete criteria reference missing achievement '.reset($missingTargets).'.'
                );
            }
        }

        if ((int) ($data['enabled'] ?? 0) === 1) {
            $this->assertNoDependencyCycle($connection, $achievementId, $data);
        }
    }

    private function assertNoDependencyCycle(
        ConnectionInterface $connection,
        int $achievementId,
        array $data
    ): void {
        $edges = [];
        $rows = $connection->table('achievement_criteria AS criterion')
            ->join('achievements AS definition', 'definition.id', '=', 'criterion.achievement_id')
            ->where('definition.enabled', 1)
            ->where('criterion.enabled', 1)
            ->where('criterion.event_type', 11)
            ->where('criterion.target_id', '>', 0)
            ->where('criterion.achievement_id', '!=', $achievementId)
            ->get(['criterion.achievement_id', 'criterion.target_id']);
        foreach ($rows as $row) {
            $edges[(int) $row->achievement_id][] = (int) $row->target_id;
        }
        foreach (($data['components'] ?? []) as $component) {
            foreach (($component['criteria'] ?? []) as $criterion) {
                if (
                    (int) $criterion['enabled'] === 1
                    && (int) $criterion['event_type'] === 11
                    && (int) $criterion['target_id'] > 0
                ) {
                    $edges[$achievementId][] = (int) $criterion['target_id'];
                }
            }
        }

        $visiting = [];
        $visited = [];
        $walk = function (int $node) use (&$walk, &$visiting, &$visited, $edges): bool {
            if (isset($visiting[$node])) {
                return true;
            }
            if (isset($visited[$node])) {
                return false;
            }
            $visiting[$node] = true;
            foreach ($edges[$node] ?? [] as $target) {
                if ($walk($target)) {
                    return true;
                }
            }
            unset($visiting[$node]);
            $visited[$node] = true;

            return false;
        };

        if ($walk($achievementId)) {
            $this->fail('components', 'Enabled achievement-completion criteria would create a dependency cycle.');
        }
    }

    private function assertCategoryParent(ConnectionInterface $connection, int $categoryId, int $parentId): void
    {
        $parents = $connection->table('achievement_categories')
            ->lockForUpdate()
            ->pluck('parent_id', 'id')
            ->mapWithKeys(fn ($value, $key) => [(int) $key => (int) $value])
            ->all();
        $parents[$categoryId] = $parentId;
        if ($parentId !== 0 && ! array_key_exists($parentId, $parents)) {
            $this->fail('parent_id', 'The selected parent category does not exist.');
        }
        $this->assertValidCategoryLineage($parents, $categoryId, 'parent_id');
    }

    private function assertValidCategoryLineage(array $parents, int $categoryId, string $errorKey): void
    {
        $seen = [];
        $cursor = $categoryId;
        while ($cursor !== 0) {
            if (isset($seen[$cursor])) {
                $this->fail($errorKey, 'Achievement categories cannot contain a parent cycle.');
            }
            $seen[$cursor] = true;
            if (! array_key_exists($cursor, $parents)) {
                $this->fail($errorKey, 'The category hierarchy contains a missing parent.');
            }
            $cursor = (int) $parents[$cursor];
        }
    }

    /**
     * @return array<int, string>
     */
    private function existingGraphRewardIds(
        ConnectionInterface $connection,
        int $achievementId,
        ?int $rewardSetId
    ): array {
        $ids = $connection->table('reward_source_entries')
            ->where('source_type', self::REWARD_SOURCE_ACHIEVEMENT)
            ->where('source_id', $achievementId)
            ->lockForUpdate()
            ->pluck('reward_id');
        if ($rewardSetId !== null) {
            $ids = $ids->concat(
                $connection->table('reward_option_entries')
                    ->where('reward_set_id', $rewardSetId)
                    ->lockForUpdate()
                    ->pluck('reward_id')
            );
        }

        return $ids->map(fn ($id) => (string) $id)->unique()->values()->all();
    }

    private function rewardPayloadRow(object $row, ?int $optionId): array
    {
        return [
            'reward_id' => (string) $row->reward_id,
            'sequence' => (int) $row->sequence,
            'reward_type' => (int) $row->reward_type,
            'reward_data_id' => (int) $row->reward_data_id,
            'amount' => (string) $row->amount,
            'description' => (string) $row->description,
            'enabled' => (int) $row->enabled,
            'option_id' => $optionId,
        ];
    }

    private function rewardCanonicalRow(array $reward): array
    {
        return [
            'reward_type' => (int) $reward['reward_type'],
            'reward_data_id' => (int) $reward['reward_data_id'],
            'amount' => (string) $reward['amount'],
            'description' => (string) ($reward['description'] ?? ''),
            'enabled' => (int) $reward['enabled'],
        ];
    }

    private function rewardRowMatches(object $stored, array $submitted): bool
    {
        return (int) $stored->reward_type === $submitted['reward_type']
            && (int) $stored->reward_data_id === $submitted['reward_data_id']
            && (string) $stored->amount === (string) $submitted['amount']
            && (string) $stored->description === $submitted['description']
            && (int) $stored->enabled === $submitted['enabled'];
    }

    private function rewardIsReferencedOutsideGraph(
        ConnectionInterface $connection,
        int $rewardId,
        int $achievementId,
        ?int $rewardSetId
    ): bool {
        $otherAutomatic = $connection->table('reward_source_entries')
            ->where('reward_id', $rewardId)
            ->where(function ($query) use ($achievementId): void {
                $query->where('source_type', '!=', self::REWARD_SOURCE_ACHIEVEMENT)
                    ->orWhere('source_id', '!=', $achievementId);
            })
            ->lockForUpdate()
            ->exists();
        if ($otherAutomatic) {
            return true;
        }

        $otherOption = $connection->table('reward_option_entries')
            ->where('reward_id', $rewardId);
        if ($rewardSetId !== null) {
            $otherOption->where('reward_set_id', '!=', $rewardSetId);
        }

        return $otherOption->lockForUpdate()->exists();
    }

    private function assertSharedRewardSetUnchanged(
        ConnectionInterface $connection,
        int $rewardSetId,
        array $rewardSet,
        array $rewards
    ): void {
        $storedSet = $connection->table('reward_sets')
            ->where('reward_set_id', $rewardSetId)
            ->lockForUpdate()
            ->first();
        $storedOptions = $connection->table('reward_options')
            ->where('reward_set_id', $rewardSetId)
            ->orderBy('sequence')
            ->orderBy('option_id')
            ->lockForUpdate()
            ->get()
            ->map(fn (object $option) => [
                'option_id' => (int) $option->option_id,
                'sequence' => (int) $option->sequence,
                'label' => (string) $option->label,
                'common_to_all' => (int) $option->common_to_all,
                'flags' => (int) $option->flags,
                'enabled' => (int) $option->enabled,
            ])
            ->values()
            ->all();
        $submittedOptions = collect($rewardSet['options'] ?? [])
            ->map(fn (array $option) => [
                'option_id' => (int) $option['option_id'],
                'sequence' => (int) $option['sequence'],
                'label' => (string) ($option['label'] ?? ''),
                'common_to_all' => (int) $option['common_to_all'],
                'flags' => (int) $option['flags'],
                'enabled' => (int) $option['enabled'],
            ])
            ->sortBy([['sequence', 'asc'], ['option_id', 'asc']])
            ->values()
            ->all();
        $storedEntries = $connection->table('reward_option_entries')
            ->where('reward_set_id', $rewardSetId)
            ->orderBy('option_id')
            ->orderBy('sequence')
            ->orderBy('reward_id')
            ->lockForUpdate()
            ->get()
            ->map(fn (object $entry) => [
                'option_id' => (int) $entry->option_id,
                'sequence' => (int) $entry->sequence,
                'reward_id' => (string) $entry->reward_id,
            ])
            ->values()
            ->all();
        $submittedEntries = collect($rewards)
            ->filter(fn (array $reward) => ($reward['option_id'] ?? null) !== null)
            ->map(fn (array $reward) => [
                'option_id' => (int) $reward['option_id'],
                'sequence' => (int) $reward['sequence'],
                'reward_id' => isset($reward['reward_id']) ? (string) $reward['reward_id'] : '',
            ])
            ->sortBy([['option_id', 'asc'], ['sequence', 'asc'], ['reward_id', 'asc']])
            ->values()
            ->all();

        if (
            ! $storedSet
            || (string) $storedSet->title !== (string) ($rewardSet['title'] ?? '')
            || (int) $storedSet->enabled !== (int) $rewardSet['enabled']
            || $storedOptions !== $submittedOptions
            || $storedEntries !== $submittedEntries
        ) {
            $this->fail(
                'reward_set',
                'This reward set is shared by another source. Enter an unused reward-set ID to fork it before changing its title, options, or mappings; replace a grant with a new row before changing shared canonical grant data.'
            );
        }
    }

    private function achievementRow(array $data, bool $includeId = true): array
    {
        $row = [
            'name' => (string) $data['name'],
            'description' => (string) ($data['description'] ?? ''),
            'icon_id' => (int) $data['icon_id'],
            'points' => (int) $data['points'],
            'has_reward' => (int) $data['has_reward'],
            'client_flag' => (int) $data['client_flag'],
            'version' => (int) $data['version'],
            'reset_on_version_change' => (int) $data['reset_on_version_change'],
            'enabled' => (int) $data['enabled'],
        ];
        if ($includeId) {
            $row = ['id' => (int) $data['id']] + $row;
        }

        return $row;
    }

    private function categoryRow(array $data, bool $includeId = true): array
    {
        $row = [
            'parent_id' => (int) $data['parent_id'],
            'sequence' => (int) $data['sequence'],
            'name' => (string) $data['name'],
            'description' => (string) ($data['description'] ?? ''),
            'icon' => (string) ($data['icon'] ?? ''),
        ];
        if ($includeId) {
            $row = ['id' => (int) $data['id']] + $row;
        }

        return $row;
    }

    private function suggestedId(ConnectionInterface $connection, string $table, string $column): int
    {
        $max = (int) ($connection->table($table)->max($column) ?? 0);

        return $max >= self::UINT32_MAX ? self::UINT32_MAX : $max + 1;
    }

    private function suggestedAchievementId(ConnectionInterface $connection): int
    {
        $max = $this->highestAchievementId($connection);

        return $max >= self::UINT32_MAX ? self::UINT32_MAX : $max + 1;
    }

    private function allocateAchievementId(ConnectionInterface $connection): int
    {
        $max = $this->highestAchievementId($connection, true);
        if ($max >= self::UINT32_MAX) {
            $this->fail('id', 'No unsigned 32-bit achievement IDs remain above existing content or preserved character state.');
        }

        return $max + 1;
    }

    private function highestAchievementId(
        ConnectionInterface $connection,
        bool $lock = false
    ): int {
        $query = $connection->table('achievements');
        if ($lock) {
            $query->lockForUpdate();
        }
        $max = (int) ($query->max('id') ?? 0);

        foreach (self::CHARACTER_ACHIEVEMENT_STATE_TABLES as $table) {
            $query = $connection->table($table);
            if ($lock) {
                $query->lockForUpdate();
            }
            $max = max($max, (int) ($query->max('achievement_id') ?? 0));
        }

        return $max;
    }

    private function achievementIdHasCharacterState(
        ConnectionInterface $connection,
        int $achievementId,
        bool $lock = false
    ): bool {
        foreach (self::CHARACTER_ACHIEVEMENT_STATE_TABLES as $table) {
            $query = $connection->table($table)->where('achievement_id', $achievementId);
            if ($lock) {
                $query->lockForUpdate();
            }
            if ($query->exists()) {
                return true;
            }
        }

        return false;
    }

    private function allocateId(
        ConnectionInterface $connection,
        string $table,
        string $column,
        string $errorKey
    ): int {
        $max = (int) ($connection->table($table)->lockForUpdate()->max($column) ?? 0);
        if ($max >= self::UINT32_MAX) {
            $this->fail($errorKey, "No unsigned 32-bit IDs remain in {$table}.{$column}.");
        }

        return $max + 1;
    }

    private function componentKey(mixed $componentType, mixed $componentId): string
    {
        return (int) $componentType.':'.(int) $componentId;
    }

    private function fail(string $key, string $message): never
    {
        throw ValidationException::withMessages([$key => $message]);
    }

    private function notFound(string $model, int $id): never
    {
        throw (new ModelNotFoundException)->setModel($model, [$id]);
    }
}
