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
        $suggestedAchievementId = $this->suggestedId($connection, 'achievements', 'id');
        $suggestedComponentId = $this->suggestedId(
            $connection,
            'achievement_component_counts',
            'component_id'
        );
        $suggestedRewardSetId = $this->suggestedId($connection, 'achievement_reward_sets', 'reward_set_id');

        if ($achievementId === null) {
            return [
                'id' => $suggestedAchievementId,
                'name' => '',
                'description' => '',
                'icon_id' => 0,
                'points' => 0,
                'reward_display' => 0,
                'world_display_flag' => 0,
                'definition_version' => 1,
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
                'achievement_component_counts AS count',
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
                'component.description',
                'component.description_2',
                'count.required_count AS presentation_count',
            ])
            ->map(function (object $row) use ($criteriaByComponent) {
                $key = $this->componentKey($row->component_type, $row->component_id);

                return [
                    'component_type' => (int) $row->component_type,
                    'sequence' => (int) $row->sequence,
                    'component_id' => (int) $row->component_id,
                    'description' => (string) $row->description,
                    'description_2' => (string) $row->description_2,
                    'presentation_count' => max(1, (int) ($row->presentation_count ?? 1)),
                    'criteria' => $criteriaByComponent[$key] ?? [],
                ];
            })
            ->all();

        $rewards = $connection->table('achievement_rewards AS reward')
            ->leftJoin(
                'achievement_reward_option_entries AS mapping',
                'mapping.reward_id',
                '=',
                'reward.reward_id'
            )
            ->where('reward.achievement_id', $achievementId)
            ->orderBy('reward.sequence')
            ->orderBy('reward.reward_id')
            ->get([
                'reward.reward_id',
                'reward.sequence',
                'reward.reward_type',
                'reward.reward_data_id',
                'reward.amount',
                'reward.description',
                'reward.enabled',
                'mapping.option_id',
            ])
            ->map(fn (object $row) => [
                'reward_id' => (string) $row->reward_id,
                'sequence' => (int) $row->sequence,
                'reward_type' => (int) $row->reward_type,
                'reward_data_id' => (int) $row->reward_data_id,
                'amount' => (string) $row->amount,
                'description' => (string) $row->description,
                'enabled' => (int) $row->enabled,
                'option_id' => $row->option_id === null ? null : (int) $row->option_id,
            ])
            ->all();

        $rewardSetRow = $connection->table('achievement_reward_sets')
            ->where('achievement_id', $achievementId)
            ->first();
        $rewardSet = null;
        if ($rewardSetRow) {
            $rewardSetId = (int) $rewardSetRow->reward_set_id;
            $rewardSet = [
                'reward_set_id' => $rewardSetId,
                'title' => (string) $rewardSetRow->title,
                'enabled' => (int) $rewardSetRow->enabled,
                'options' => $connection->table('achievement_reward_options')
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
        }

        $restrictions = $connection->table('achievement_cast_restrictions')
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
            'reward_display' => (int) $definition->reward_display,
            'world_display_flag' => (int) $definition->world_display_flag,
            'definition_version' => (int) $definition->definition_version,
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
            $newAchievementId = $this->allocateId($connection, 'achievements', 'id', 'id');
            $data['id'] = $newAchievementId;
            $data['name'] = Str::limit((string) $data['name'], 248, '').' (Copy)';
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

            $rewardIds = $connection->table('achievement_rewards')
                ->where('achievement_id', $achievementId)
                ->lockForUpdate()
                ->pluck('reward_id')
                ->all();
            $rewardSetIds = $connection->table('achievement_reward_sets')
                ->where('achievement_id', $achievementId)
                ->lockForUpdate()
                ->pluck('reward_set_id')
                ->all();

            if ($rewardIds !== []) {
                $connection->table('achievement_reward_option_entries')
                    ->whereIn('reward_id', $rewardIds)
                    ->delete();
            }
            if ($rewardSetIds !== []) {
                $connection->table('achievement_reward_option_entries')
                    ->whereIn('reward_set_id', $rewardSetIds)
                    ->delete();
                $connection->table('achievement_reward_options')
                    ->whereIn('reward_set_id', $rewardSetIds)
                    ->delete();
            }

            $connection->table('achievement_reward_sets')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_rewards')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_cast_restrictions')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_criteria')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_components')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievement_category_associations')->where('achievement_id', $achievementId)->delete();
            $connection->table('achievements')->where('id', $achievementId)->delete();

            // Character completion/progress/reward ledgers and the global
            // component-count table are intentionally retained as history.
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

            $storedCount = $connection->table('achievement_component_counts')
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
                'description' => (string) ($component['description'] ?? ''),
                'description_2' => (string) ($component['description_2'] ?? ''),
            ];

            // component_id is a deliberately global presentation identity.
            // Never delete its count merely because this aggregate stopped
            // using it; edit/create only upserts the submitted value.
            $connection->table('achievement_component_counts')->updateOrInsert(
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
        $existingRewardIds = $connection->table('achievement_rewards')
            ->where('achievement_id', $achievementId)
            ->lockForUpdate()
            ->pluck('reward_id')
            ->map(fn ($id) => (string) $id)
            ->all();
        $existingRewardIdMap = array_fill_keys($existingRewardIds, true);
        foreach ($rewards as $index => $reward) {
            if (($reward['reward_id'] ?? null) === null) {
                continue;
            }
            if (! isset($existingRewardIdMap[(string) $reward['reward_id']])) {
                $this->fail(
                    "rewards.{$index}.reward_id",
                    'Existing reward IDs are immutable and cannot be adopted from another achievement.'
                );
            }
        }

        $existingSet = $connection->table('achievement_reward_sets')
            ->where('achievement_id', $achievementId)
            ->lockForUpdate()
            ->first();
        $rewardSetId = null;
        if ($rewardSet !== null) {
            $requestedSetId = $rewardSet['reward_set_id'] ?? null;
            if ($existingSet) {
                if ($requestedSetId === null || (int) $requestedSetId !== (int) $existingSet->reward_set_id) {
                    $this->fail(
                        'reward_set.reward_set_id',
                        'The stable reward-set ID cannot be changed after creation.'
                    );
                }
                $rewardSetId = (int) $existingSet->reward_set_id;
            } elseif ($requestedSetId !== null) {
                $rewardSetId = (int) $requestedSetId;
                if (
                    $connection->table('achievement_reward_sets')
                        ->where('reward_set_id', $rewardSetId)
                        ->lockForUpdate()
                        ->exists()
                ) {
                    $this->fail('reward_set.reward_set_id', 'That reward-set ID is already in use.');
                }
            } else {
                $rewardSetId = $this->allocateId(
                    $connection,
                    'achievement_reward_sets',
                    'reward_set_id',
                    'reward_set.reward_set_id'
                );
            }
        }

        if ($existingRewardIds !== []) {
            $connection->table('achievement_reward_option_entries')
                ->whereIn('reward_id', $existingRewardIds)
                ->delete();
        }
        if ($existingSet) {
            $connection->table('achievement_reward_option_entries')
                ->where('reward_set_id', $existingSet->reward_set_id)
                ->delete();
            $connection->table('achievement_reward_options')
                ->where('reward_set_id', $existingSet->reward_set_id)
                ->delete();
            $connection->table('achievement_reward_sets')
                ->where('reward_set_id', $existingSet->reward_set_id)
                ->delete();
        }
        // Reinsert retained canonical IDs so sequence swaps cannot collide
        // with the unique (achievement_id, sequence) key.
        $connection->table('achievement_rewards')->where('achievement_id', $achievementId)->delete();

        $savedRewards = [];
        foreach ($rewards as $index => $reward) {
            $row = [
                'achievement_id' => $achievementId,
                'sequence' => (int) $reward['sequence'],
                'reward_type' => (int) $reward['reward_type'],
                'reward_data_id' => (int) $reward['reward_data_id'],
                'amount' => (int) $reward['amount'],
                'description' => (string) ($reward['description'] ?? ''),
                'enabled' => (int) $reward['enabled'],
            ];

            if (($reward['reward_id'] ?? null) !== null) {
                $rewardId = (int) $reward['reward_id'];
                $connection->table('achievement_rewards')->insert(['reward_id' => $rewardId] + $row);
            } else {
                $rewardId = (int) $connection->table('achievement_rewards')
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
                'option_id' => $reward['option_id'] ?? null,
            ];
        }

        if ($rewardSet === null) {
            foreach ($savedRewards as $index => $reward) {
                if ($reward['option_id'] !== null) {
                    $this->fail("rewards.{$index}.option_id", 'A reward mapping requires a reward set.');
                }
            }

            return;
        }

        $connection->table('achievement_reward_sets')->insert([
            'reward_set_id' => $rewardSetId,
            'achievement_id' => $achievementId,
            'title' => (string) ($rewardSet['title'] ?? ''),
            'enabled' => (int) $rewardSet['enabled'],
        ]);

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
            $connection->table('achievement_reward_options')->insert($optionRows);
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
                'reward_id' => $reward['reward_id'],
            ];
        }
        if ($mappingRows !== []) {
            $connection->table('achievement_reward_option_entries')->insert($mappingRows);
        }
    }

    private function syncRestrictions(ConnectionInterface $connection, int $achievementId, array $restrictions): void
    {
        $connection->table('achievement_cast_restrictions')
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
            $connection->table('achievement_cast_restrictions')->insert($rows);
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

    private function achievementRow(array $data, bool $includeId = true): array
    {
        $row = [
            'name' => (string) $data['name'],
            'description' => (string) ($data['description'] ?? ''),
            'icon_id' => (int) $data['icon_id'],
            'points' => (int) $data['points'],
            'reward_display' => (int) $data['reward_display'],
            'world_display_flag' => (int) $data['world_display_flag'],
            'definition_version' => (int) $data['definition_version'],
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
