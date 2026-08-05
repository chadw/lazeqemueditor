<?php

namespace App\Services;

use App\Exceptions\CharacterAchievementMutationException;
use App\Models\CharacterData;
use App\Support\Achievements\AchievementMetadata;
use Closure;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Throwable;

class CharacterAchievementService
{
    public const DURABLE_STATES = [
        'all' => 'All achievements',
        'completed' => 'Completed',
        'not_completed' => 'Not completed',
        'in_progress' => 'In progress',
        'not_started' => 'Not started',
        'version_mismatch' => 'Definition version mismatch',
        'reward_attention' => 'Reward ledger needs attention',
        'pending_mutation' => 'Has queued mutation',
    ];

    private const UINT32_MAX = 4_294_967_295;

    private ConnectionInterface $connection;

    public function __construct(DatabaseManager $database)
    {
        $this->connection = $database->connection('eqemu');
    }

    public function paginateCharacters(?string $search = null): LengthAwarePaginator
    {
        $completionCount = $this->connection
            ->table('character_achievements as completion_count')
            ->selectRaw('COUNT(*)')
            ->whereColumn('completion_count.character_id', 'character_data.id');
        $progressCount = $this->connection
            ->table('character_achievement_progress as progress_count')
            ->selectRaw('COUNT(*)')
            ->whereColumn('progress_count.character_id', 'character_data.id')
            ->where('progress_count.current_count', '>', 0);
        $progressRowCount = $this->connection
            ->table('character_achievement_progress as progress_row_count')
            ->selectRaw('COUNT(*)')
            ->whereColumn('progress_row_count.character_id', 'character_data.id');
        $progressTotal = $this->connection
            ->table('character_achievement_progress as progress_total')
            ->selectRaw('COALESCE(SUM(progress_total.current_count), 0)')
            ->whereColumn('progress_total.character_id', 'character_data.id');

        $query = CharacterData::query()
            ->select('character_data.*')
            ->addSelect([
                'achievement_completion_count' => $completionCount,
                'achievement_progress_count' => $progressCount,
                'achievement_progress_row_count' => $progressRowCount,
                'achievement_progress_total' => $progressTotal,
            ])
            ->whereNull('character_data.deleted_at');

        $search = trim((string) $search);
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                if (ctype_digit($search)) {
                    $query->orWhere('character_data.id', (int) $search);
                }

                $query->orWhere('character_data.name', 'like', "%{$search}%");
            });
        }

        return $query
            ->orderBy('character_data.name')
            ->orderBy('character_data.id')
            ->paginate(50)
            ->withQueryString();
    }

    /**
     * @return array{
     *     achievements: LengthAwarePaginator,
     *     categories: Collection<int, object>,
     *     metadata: array<string, mixed>
     * }
     */
    public function catalog(int $characterId, array $filters): array
    {
        $search = trim((string) ($filters['q'] ?? ''));
        $categoryId = isset($filters['category']) && $filters['category'] !== ''
            ? (int) $filters['category']
            : null;
        $durableState = (string) ($filters['state'] ?? 'all');

        if (! array_key_exists($durableState, self::DURABLE_STATES)) {
            $durableState = 'all';
        }

        $query = $this->connection
            ->table('achievements as a')
            ->select('a.*');

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                if (ctype_digit($search)) {
                    $query->orWhere('a.id', (int) $search);
                }

                $query
                    ->orWhere('a.name', 'like', "%{$search}%")
                    ->orWhere('a.description', 'like', "%{$search}%");
            });
        }

        if ($categoryId !== null) {
            $query->whereExists(function ($query) use ($categoryId) {
                $query
                    ->selectRaw('1')
                    ->from('achievement_category_associations as filtered_category')
                    ->whereColumn('filtered_category.achievement_id', 'a.id')
                    ->where('filtered_category.category_id', $categoryId);
            });
        }

        $this->applyDurableStateFilter($query, $characterId, $durableState);

        $achievements = $query
            ->orderByDesc('a.enabled')
            ->orderBy('a.name')
            ->orderBy('a.id')
            ->paginate(25)
            ->withQueryString();

        $achievements->setCollection(
            $this->hydrateCatalogPage($achievements->getCollection(), $characterId)
        );

        $categories = $this->connection
            ->table('achievement_categories')
            ->orderBy('parent_id')
            ->orderBy('sequence')
            ->orderBy('id')
            ->get();

        return [
            'achievements' => $achievements,
            'categories' => $categories,
            'metadata' => [
                'filters' => [
                    'q' => $search,
                    'category' => $categoryId,
                    'state' => $durableState,
                ],
                'durable_states' => self::DURABLE_STATES,
                'component_types' => AchievementMetadata::COMPONENT_TYPES,
                'reward_statuses' => AchievementMetadata::CHARACTER_REWARD_STATUSES,
                'selection_statuses' => AchievementMetadata::CHARACTER_SELECTION_STATUSES,
                'mutation_statuses' => AchievementMetadata::CHARACTER_MUTATION_STATUSES,
                'mutation_operations' => AchievementMetadata::MUTATION_OPERATIONS,
                'mutation_target_types' => AchievementMetadata::MUTATION_TARGET_TYPES,
                'mutation_processing_lease_seconds' => AchievementMetadata::MUTATION_PROCESSING_LEASE_SECONDS,
                'force_completion_warning' => 'Offline force-completion writes durable completion only. It does not send a live earned notification or immediately run reward and dependency side effects; the server reconciles durable state when the character next loads.',
            ],
        ];
    }

    /**
     * @return array{requested_count: int, current_count: int, required_count: int, completed: bool}
     */
    public function setExactProgress(
        int $characterId,
        int $achievementId,
        int $componentType,
        int $componentId,
        int $requestedCount
    ): array {
        return $this->withCharacterMutation(
            $characterId,
            function (ConnectionInterface $connection) use (
                $characterId,
                $achievementId,
                $componentType,
                $componentId,
                $requestedCount
            ) {
                $achievement = $this->enabledAchievement($connection, $achievementId);

                $alreadyCompleted = $connection
                    ->table('character_achievements')
                    ->where('character_id', $characterId)
                    ->where('achievement_id', $achievementId)
                    ->lockForUpdate()
                    ->first();
                if ($alreadyCompleted) {
                    throw new CharacterAchievementMutationException(
                        'Completed achievements cannot receive component progress. Reset it first.'
                    );
                }

                if (! AchievementMetadata::isStateBearingComponentType($componentType)) {
                    throw new CharacterAchievementMutationException(
                        'Only state-bearing component types 0 through 2 can store progress.'
                    );
                }

                $component = $connection
                    ->table('achievement_components as component')
                    ->leftJoin(
                        'achievement_component_counts as presentation_count',
                        'presentation_count.component_id',
                        '=',
                        'component.component_id'
                    )
                    ->where('component.achievement_id', $achievementId)
                    ->where('component.component_type', $componentType)
                    ->where('component.component_id', $componentId)
                    ->select([
                        'component.sequence',
                        'presentation_count.required_count as presentation_required_count',
                    ])
                    ->first();
                if (! $component) {
                    throw new CharacterAchievementMutationException(
                        'The requested component does not belong to this achievement.'
                    );
                }

                $requiredCount = $this->effectiveRequiredCount(
                    $connection,
                    $achievementId,
                    $componentType,
                    $componentId,
                    $component->presentation_required_count
                );
                $currentCount = min(max($requestedCount, 0), $requiredCount);
                $completed = $currentCount >= $requiredCount;
                $now = now()->getTimestamp();
                $identity = [
                    'character_id' => $characterId,
                    'achievement_id' => $achievementId,
                    'component_type' => $componentType,
                    'component_id' => $componentId,
                ];
                $values = [
                    'component_sequence' => (int) $component->sequence,
                    'current_count' => $currentCount,
                    'completed' => $completed ? 1 : 0,
                    'definition_version' => (int) $achievement->definition_version,
                    'updated_at' => $now,
                ];

                $progress = $connection
                    ->table('character_achievement_progress')
                    ->where($identity)
                    ->lockForUpdate()
                    ->first();
                if ($progress) {
                    $connection
                        ->table('character_achievement_progress')
                        ->where($identity)
                        ->update($values);
                } else {
                    $connection
                        ->table('character_achievement_progress')
                        ->insert(array_merge($identity, $values));
                }

                return [
                    'requested_count' => $requestedCount,
                    'current_count' => $currentCount,
                    'required_count' => $requiredCount,
                    'completed' => $completed,
                ];
            }
        );
    }

    /**
     * @return array{definition_version: int, completed_at: int}
     */
    public function forceCompleteOffline(int $characterId, int $achievementId): array
    {
        return $this->withCharacterMutation(
            $characterId,
            function (ConnectionInterface $connection) use ($characterId, $achievementId) {
                $character = $connection
                    ->table('character_data')
                    ->where('id', $characterId)
                    ->select(['id', 'ingame'])
                    ->lockForUpdate()
                    ->first();
                if (! $character) {
                    throw new CharacterAchievementMutationException('Character not found.');
                }
                if ((int) $character->ingame !== 0) {
                    throw new CharacterAchievementMutationException(
                        'Force-completion is offline-only. Log the character out before continuing.'
                    );
                }

                $achievement = $this->enabledAchievement($connection, $achievementId);
                $completion = $connection
                    ->table('character_achievements')
                    ->where('character_id', $characterId)
                    ->where('achievement_id', $achievementId)
                    ->lockForUpdate()
                    ->first();
                if ($completion) {
                    throw new CharacterAchievementMutationException(
                        'This character has already completed the achievement.'
                    );
                }

                $completedAt = now()->getTimestamp();
                $connection->table('character_achievements')->insert([
                    'character_id' => $characterId,
                    'achievement_id' => $achievementId,
                    'definition_version' => (int) $achievement->definition_version,
                    'completed_at' => $completedAt,
                ]);

                return [
                    'definition_version' => (int) $achievement->definition_version,
                    'completed_at' => $completedAt,
                ];
            }
        );
    }

    /**
     * @return array<string, int>
     */
    public function reset(int $characterId, int $achievementId, bool $resetRewards = false): array
    {
        return $this->withCharacterMutation(
            $characterId,
            function (ConnectionInterface $connection) use (
                $characterId,
                $achievementId,
                $resetRewards
            ) {
                $achievementExists = $connection
                    ->table('achievements')
                    ->where('id', $achievementId)
                    ->lockForUpdate()
                    ->exists();
                if (! $achievementExists) {
                    throw new CharacterAchievementMutationException(
                        'The achievement definition no longer exists.'
                    );
                }

                $scope = static fn ($query) => $query
                    ->where('character_id', $characterId)
                    ->where('achievement_id', $achievementId);

                $deleted = [
                    'pending_mutations' => $scope(
                        $connection->table('character_achievement_pending_mutations')
                    )->delete(),
                    'progress' => $scope(
                        $connection->table('character_achievement_progress')
                    )->delete(),
                    'completion' => $scope(
                        $connection->table('character_achievements')
                    )->delete(),
                    'reward_selections' => 0,
                    'rewards' => 0,
                ];

                if ($resetRewards) {
                    $deleted['reward_selections'] = $scope(
                        $connection->table('character_achievement_reward_selections')
                    )->delete();
                    $deleted['rewards'] = $scope(
                        $connection->table('character_achievement_rewards')
                    )->delete();
                }

                return $deleted;
            }
        );
    }

    public function markRewardRetryable(
        int $characterId,
        int $achievementId,
        int $rewardId
    ): void {
        $this->withCharacterMutation(
            $characterId,
            function (ConnectionInterface $connection) use (
                $characterId,
                $achievementId,
                $rewardId
            ) {
                $rewardBelongsToAchievement = $connection
                    ->table('achievement_rewards')
                    ->where('reward_id', $rewardId)
                    ->where('achievement_id', $achievementId)
                    ->exists();
                if (! $rewardBelongsToAchievement) {
                    throw new CharacterAchievementMutationException(
                        'The requested reward does not belong to this achievement.'
                    );
                }

                $query = $connection
                    ->table('character_achievement_rewards')
                    ->where('character_id', $characterId)
                    ->where('achievement_id', $achievementId)
                    ->where('reward_id', $rewardId);
                $ledger = (clone $query)->lockForUpdate()->first();
                if (! $ledger) {
                    throw new CharacterAchievementMutationException(
                        'The character does not own this reward ledger.'
                    );
                }
                if ((int) $ledger->status === AchievementMetadata::CHARACTER_REWARD_STATUS_GRANTED) {
                    throw new CharacterAchievementMutationException(
                        'A granted reward cannot be marked retryable. Use an explicit reward reset if re-granting is intended.'
                    );
                }
                if (! in_array((int) $ledger->status, [
                    AchievementMetadata::CHARACTER_REWARD_STATUS_IN_FLIGHT,
                    AchievementMetadata::CHARACTER_REWARD_STATUS_RETRYABLE_FAILURE,
                ], true)) {
                    throw new CharacterAchievementMutationException(
                        'Only an in-flight or explicitly failed reward can be marked retryable.'
                    );
                }

                $query->update([
                    'status' => AchievementMetadata::CHARACTER_REWARD_STATUS_RETRYABLE_FAILURE,
                    'last_error' => 'manually marked retryable; duplicate-delivery risk explicitly accepted',
                ]);
            }
        );
    }

    public function markSelectionRetryable(
        int $characterId,
        int $achievementId,
        int $rewardSetId
    ): void {
        $this->withCharacterMutation(
            $characterId,
            function (ConnectionInterface $connection) use (
                $characterId,
                $achievementId,
                $rewardSetId
            ) {
                $setBelongsToAchievement = $connection
                    ->table('achievement_reward_sets')
                    ->where('reward_set_id', $rewardSetId)
                    ->where('achievement_id', $achievementId)
                    ->exists();
                if (! $setBelongsToAchievement) {
                    throw new CharacterAchievementMutationException(
                        'The requested reward set does not belong to this achievement.'
                    );
                }

                $query = $connection
                    ->table('character_achievement_reward_selections')
                    ->where('character_id', $characterId)
                    ->where('achievement_id', $achievementId)
                    ->where('reward_set_id', $rewardSetId);
                $selection = (clone $query)->lockForUpdate()->first();
                if (! $selection) {
                    throw new CharacterAchievementMutationException(
                        'The character does not own this reward-selection ledger.'
                    );
                }
                if ((int) $selection->status === AchievementMetadata::CHARACTER_SELECTION_STATUS_GRANTED) {
                    throw new CharacterAchievementMutationException(
                        'A fully granted selection cannot be marked retryable. Use an explicit reward reset if re-granting is intended.'
                    );
                }
                if (
                    (int) $selection->status === AchievementMetadata::CHARACTER_SELECTION_STATUS_PENDING
                    && (int) $selection->selected_option_id === 0
                ) {
                    throw new CharacterAchievementMutationException(
                        'An unselected reward is already pending and does not need a retry override.'
                    );
                }
                if (! in_array((int) $selection->status, [
                    AchievementMetadata::CHARACTER_SELECTION_STATUS_PENDING,
                    AchievementMetadata::CHARACTER_SELECTION_STATUS_RETRYABLE_FAILURE,
                    AchievementMetadata::CHARACTER_SELECTION_STATUS_AMBIGUOUS,
                ], true)) {
                    throw new CharacterAchievementMutationException(
                        'Only an in-flight, failed, or ambiguous selection can be marked retryable.'
                    );
                }

                $query->update([
                    'status' => AchievementMetadata::CHARACTER_SELECTION_STATUS_RETRYABLE_FAILURE,
                    'last_error' => 'manually marked retryable; duplicate-delivery risk explicitly accepted',
                ]);
            }
        );
    }

    public function retryBlockedMutation(
        int $characterId,
        int $achievementId,
        int $mutationId
    ): void {
        $this->withCharacterMutation(
            $characterId,
            function (ConnectionInterface $connection) use (
                $characterId,
                $achievementId,
                $mutationId
            ) {
                $query = $connection
                    ->table('character_achievement_pending_mutations')
                    ->where('mutation_id', $mutationId)
                    ->where('character_id', $characterId)
                    ->where('achievement_id', $achievementId);
                $mutation = (clone $query)->lockForUpdate()->first();
                if (! $mutation) {
                    throw new CharacterAchievementMutationException(
                        'The queued mutation does not belong to this character and achievement.'
                    );
                }
                if ((int) $mutation->status !== AchievementMetadata::CHARACTER_MUTATION_STATUS_BLOCKED) {
                    throw new CharacterAchievementMutationException(
                        'Only a blocked mutation can be manually retried.'
                    );
                }

                $query->update([
                    'status' => AchievementMetadata::CHARACTER_MUTATION_STATUS_PENDING,
                    'last_attempt_at' => 0,
                    'last_error' => '',
                ]);
            }
        );
    }

    public function discardMutation(
        int $characterId,
        int $achievementId,
        int $mutationId
    ): void {
        $this->withCharacterMutation(
            $characterId,
            function (ConnectionInterface $connection) use (
                $characterId,
                $achievementId,
                $mutationId
            ) {
                $query = $connection
                    ->table('character_achievement_pending_mutations')
                    ->where('mutation_id', $mutationId)
                    ->where('character_id', $characterId)
                    ->where('achievement_id', $achievementId);
                $mutation = (clone $query)->lockForUpdate()->first();
                if (! $mutation) {
                    throw new CharacterAchievementMutationException(
                        'The queued mutation does not belong to this character and achievement.'
                    );
                }

                $query->delete();
            }
        );
    }

    private function applyDurableStateFilter($query, int $characterId, string $state): void
    {
        $completionExists = static function ($query) use ($characterId) {
            $query
                ->selectRaw('1')
                ->from('character_achievements as filtered_completion')
                ->whereColumn('filtered_completion.achievement_id', 'a.id')
                ->where('filtered_completion.character_id', $characterId);
        };
        $positiveProgressExists = static function ($query) use ($characterId) {
            $query
                ->selectRaw('1')
                ->from('character_achievement_progress as filtered_progress')
                ->whereColumn('filtered_progress.achievement_id', 'a.id')
                ->where('filtered_progress.character_id', $characterId)
                ->where('filtered_progress.current_count', '>', 0);
        };

        switch ($state) {
            case 'completed':
                $query->whereExists($completionExists);
                break;
            case 'not_completed':
                $query->whereNotExists($completionExists);
                break;
            case 'in_progress':
                $query
                    ->whereNotExists($completionExists)
                    ->whereExists($positiveProgressExists);
                break;
            case 'not_started':
                $query
                    ->whereNotExists($completionExists)
                    ->whereNotExists($positiveProgressExists);
                break;
            case 'version_mismatch':
                $query->where(function ($query) use ($characterId) {
                    $query
                        ->whereExists(function ($query) use ($characterId) {
                            $query
                                ->selectRaw('1')
                                ->from('character_achievements as stale_completion')
                                ->whereColumn('stale_completion.achievement_id', 'a.id')
                                ->whereColumn('stale_completion.definition_version', '<>', 'a.definition_version')
                                ->where('stale_completion.character_id', $characterId);
                        })
                        ->orWhereExists(function ($query) use ($characterId) {
                            $query
                                ->selectRaw('1')
                                ->from('character_achievement_progress as stale_progress')
                                ->whereColumn('stale_progress.achievement_id', 'a.id')
                                ->whereColumn('stale_progress.definition_version', '<>', 'a.definition_version')
                                ->where('stale_progress.character_id', $characterId);
                        });
                });
                break;
            case 'reward_attention':
                $query->where(function ($query) use ($characterId) {
                    $query
                        ->whereExists(function ($query) use ($characterId) {
                            $query
                                ->selectRaw('1')
                                ->from('character_achievement_rewards as attention_reward')
                                ->whereColumn('attention_reward.achievement_id', 'a.id')
                                ->where('attention_reward.character_id', $characterId)
                                ->whereIn('attention_reward.status', [0, 2]);
                        })
                        ->orWhereExists(function ($query) use ($characterId) {
                            $query
                                ->selectRaw('1')
                                ->from('character_achievement_reward_selections as attention_selection')
                                ->whereColumn('attention_selection.achievement_id', 'a.id')
                                ->where('attention_selection.character_id', $characterId)
                                ->where(function ($query) {
                                    $query
                                        ->whereIn('attention_selection.status', [2, 3])
                                        ->orWhere(function ($query) {
                                            $query
                                                ->where('attention_selection.status', 0)
                                                ->where('attention_selection.selected_option_id', '<>', 0);
                                        });
                                });
                        });
                });
                break;
            case 'pending_mutation':
                $query->whereExists(function ($query) use ($characterId) {
                    $query
                        ->selectRaw('1')
                        ->from('character_achievement_pending_mutations as filtered_mutation')
                        ->whereColumn('filtered_mutation.achievement_id', 'a.id')
                        ->where('filtered_mutation.character_id', $characterId);
                });
                break;
        }
    }

    /**
     * Attach only this character's state to the definitions on the current page.
     *
     * @param  Collection<int, object>  $achievements
     * @return Collection<int, object>
     */
    private function hydrateCatalogPage(Collection $achievements, int $characterId): Collection
    {
        $achievementIds = $achievements->pluck('id')->map(fn ($id) => (int) $id)->all();
        if ($achievementIds === []) {
            return $achievements;
        }

        $categories = $this->connection
            ->table('achievement_category_associations as association')
            ->join('achievement_categories as category', 'category.id', '=', 'association.category_id')
            ->whereIn('association.achievement_id', $achievementIds)
            ->select([
                'association.achievement_id',
                'association.category_id',
                'association.sequence as association_sequence',
                'association.display_text',
                'category.parent_id',
                'category.sequence as category_sequence',
                'category.name',
                'category.description',
                'category.icon',
            ])
            ->orderBy('association.sequence')
            ->orderBy('association.category_id')
            ->get()
            ->groupBy('achievement_id');

        $completions = $this->connection
            ->table('character_achievements')
            ->where('character_id', $characterId)
            ->whereIn('achievement_id', $achievementIds)
            ->get()
            ->keyBy('achievement_id');
        $progressRows = $this->connection
            ->table('character_achievement_progress')
            ->where('character_id', $characterId)
            ->whereIn('achievement_id', $achievementIds)
            ->orderBy('component_type')
            ->orderBy('component_sequence')
            ->orderBy('component_id')
            ->get();
        $progressByAchievement = $progressRows->groupBy('achievement_id');
        $progressByComponent = $progressRows->keyBy(fn ($progress) => $this->componentKey(
            (int) $progress->achievement_id,
            (int) $progress->component_type,
            (int) $progress->component_id
        ));

        $criteria = $this->connection
            ->table('achievement_criteria')
            ->whereIn('achievement_id', $achievementIds)
            ->orderBy('id')
            ->get();
        $criteriaByComponent = $criteria->groupBy(fn ($criterion) => $this->componentKey(
            (int) $criterion->achievement_id,
            (int) $criterion->component_type,
            (int) $criterion->component_id
        ));
        $components = $this->connection
            ->table('achievement_components as component')
            ->leftJoin(
                'achievement_component_counts as presentation_count',
                'presentation_count.component_id',
                '=',
                'component.component_id'
            )
            ->whereIn('component.achievement_id', $achievementIds)
            ->select([
                'component.*',
                'presentation_count.required_count as presentation_required_count',
            ])
            ->orderBy('component.achievement_id')
            ->orderBy('component.component_type')
            ->orderBy('component.sequence')
            ->orderBy('component.component_id')
            ->get();

        foreach ($components as $component) {
            $key = $this->componentKey(
                (int) $component->achievement_id,
                (int) $component->component_type,
                (int) $component->component_id
            );
            $componentCriteria = $criteriaByComponent->get($key, collect())->values();
            $criterionCounts = $componentCriteria
                ->where('enabled', 1)
                ->pluck('required_count')
                ->map(fn ($count) => (int) $count)
                ->unique()
                ->values();
            $presentationCount = $this->normalizePresentationCount(
                $component->presentation_required_count
            );

            $component->criteria = $componentCriteria;
            $component->progress = $progressByComponent->get($key);
            $component->state_bearing = AchievementMetadata::isStateBearingComponentType(
                (int) $component->component_type
            );
            $component->presentation_required_count = $presentationCount;
            $component->criterion_required_counts = $criterionCounts->all();
            $component->effective_count_conflict =
                $criterionCounts->count() > 1 || $criterionCounts->contains(fn ($count) => $count < 1);
            $component->effective_required_count = $criterionCounts->count() === 1
                ? (int) $criterionCounts->first()
                : $presentationCount;
        }
        $componentsByAchievement = $components->groupBy('achievement_id');

        $rewardLedgers = $this->connection
            ->table('character_achievement_rewards')
            ->where('character_id', $characterId)
            ->whereIn('achievement_id', $achievementIds)
            ->orderBy('reward_id')
            ->get();
        $rewardLedgersByAchievement = $rewardLedgers->groupBy('achievement_id');
        $rewardLedgerByIdentity = $rewardLedgers->keyBy(fn ($ledger) => $this->rewardKey(
            (int) $ledger->achievement_id,
            (int) $ledger->reward_id
        ));
        $rewardDefinitions = $this->connection
            ->table('achievement_rewards')
            ->whereIn('achievement_id', $achievementIds)
            ->orderBy('achievement_id')
            ->orderBy('sequence')
            ->orderBy('reward_id')
            ->get();
        foreach ($rewardDefinitions as $reward) {
            $reward->ledger = $rewardLedgerByIdentity->get($this->rewardKey(
                (int) $reward->achievement_id,
                (int) $reward->reward_id
            ));
        }
        $rewardsByAchievement = $rewardDefinitions->groupBy('achievement_id');
        $rewardById = $rewardDefinitions->keyBy('reward_id');

        $rewardSelections = $this->connection
            ->table('character_achievement_reward_selections')
            ->where('character_id', $characterId)
            ->whereIn('achievement_id', $achievementIds)
            ->orderBy('reward_set_id')
            ->get();
        $selectionsByAchievement = $rewardSelections->groupBy('achievement_id');
        $selectionByIdentity = $rewardSelections->keyBy(fn ($selection) => $this->selectionKey(
            (int) $selection->achievement_id,
            (int) $selection->reward_set_id
        ));
        $rewardSets = $this->connection
            ->table('achievement_reward_sets')
            ->whereIn('achievement_id', $achievementIds)
            ->orderBy('achievement_id')
            ->orderBy('reward_set_id')
            ->get();
        $rewardSetIds = $rewardSets->pluck('reward_set_id')->map(fn ($id) => (int) $id)->all();
        $options = collect();
        $entries = collect();
        if ($rewardSetIds !== []) {
            $options = $this->connection
                ->table('achievement_reward_options')
                ->whereIn('reward_set_id', $rewardSetIds)
                ->orderBy('reward_set_id')
                ->orderBy('sequence')
                ->orderBy('option_id')
                ->get();
            $entries = $this->connection
                ->table('achievement_reward_option_entries')
                ->whereIn('reward_set_id', $rewardSetIds)
                ->orderBy('reward_set_id')
                ->orderBy('option_id')
                ->orderBy('reward_id')
                ->get();
        }
        $entriesByOption = $entries->groupBy(fn ($entry) => $this->optionKey(
            (int) $entry->reward_set_id,
            (int) $entry->option_id
        ));
        foreach ($options as $option) {
            $option->entries = $entriesByOption
                ->get($this->optionKey((int) $option->reward_set_id, (int) $option->option_id), collect())
                ->map(function ($entry) use ($rewardById) {
                    $entry->reward = $rewardById->get($entry->reward_id);

                    return $entry;
                })
                ->values();
        }
        $optionsBySet = $options->groupBy('reward_set_id');
        foreach ($rewardSets as $set) {
            $set->options = $optionsBySet->get($set->reward_set_id, collect())->values();
            $set->selection = $selectionByIdentity->get($this->selectionKey(
                (int) $set->achievement_id,
                (int) $set->reward_set_id
            ));
        }
        $setsByAchievement = $rewardSets->groupBy('achievement_id');

        $mutationsByAchievement = $this->connection
            ->table('character_achievement_pending_mutations')
            ->where('character_id', $characterId)
            ->whereIn('achievement_id', $achievementIds)
            ->orderBy('mutation_id')
            ->get()
            ->groupBy('achievement_id');

        return $achievements->map(function ($achievement) use (
            $categories,
            $completions,
            $progressByAchievement,
            $componentsByAchievement,
            $rewardsByAchievement,
            $rewardLedgersByAchievement,
            $setsByAchievement,
            $selectionsByAchievement,
            $mutationsByAchievement
        ) {
            $achievementId = (int) $achievement->id;
            $completion = $completions->get($achievementId);
            $progress = $progressByAchievement->get($achievementId, collect())->values();
            $rewardLedgers = $rewardLedgersByAchievement->get($achievementId, collect())->values();
            $selections = $selectionsByAchievement->get($achievementId, collect())->values();
            $mutations = $mutationsByAchievement->get($achievementId, collect())->values();

            $achievement->categories = $categories->get($achievementId, collect())->values();
            $achievement->completion = $completion;
            $achievement->progress = $progress;
            $achievement->components = $componentsByAchievement->get($achievementId, collect())->values();
            $achievement->rewards = $rewardsByAchievement->get($achievementId, collect())->values();
            $achievement->reward_ledgers = $rewardLedgers;
            $achievement->reward_sets = $setsByAchievement->get($achievementId, collect())->values();
            $achievement->reward_selections = $selections;
            $achievement->pending_mutations = $mutations;
            $achievement->durable_state = $completion
                ? 'completed'
                : ($progress->contains(fn ($row) => (int) $row->current_count > 0)
                    ? 'in_progress'
                    : 'not_started');
            $achievement->has_version_mismatch =
                ($completion && (int) $completion->definition_version !== (int) $achievement->definition_version)
                || $progress->contains(
                    fn ($row) => (int) $row->definition_version !== (int) $achievement->definition_version
                );
            $achievement->reward_needs_attention =
                $rewardLedgers->contains(fn ($row) => in_array((int) $row->status, [0, 2], true))
                || $selections->contains(
                    fn ($row) => in_array((int) $row->status, [2, 3], true)
                        || ((int) $row->status === 0 && (int) $row->selected_option_id !== 0)
                );

            return $achievement;
        });
    }

    private function enabledAchievement(ConnectionInterface $connection, int $achievementId): object
    {
        $achievement = $connection
            ->table('achievements')
            ->where('id', $achievementId)
            ->where('enabled', 1)
            ->first(['id', 'definition_version']);
        if (! $achievement) {
            throw new CharacterAchievementMutationException(
                'The achievement definition is disabled or unavailable to the runtime.'
            );
        }

        return $achievement;
    }

    private function effectiveRequiredCount(
        ConnectionInterface $connection,
        int $achievementId,
        int $componentType,
        int $componentId,
        mixed $presentationRequiredCount
    ): int {
        $criterionCounts = $connection
            ->table('achievement_criteria')
            ->where('achievement_id', $achievementId)
            ->where('component_type', $componentType)
            ->where('component_id', $componentId)
            ->where('enabled', 1)
            ->pluck('required_count')
            ->map(fn ($count) => (int) $count)
            ->unique()
            ->values();

        if ($criterionCounts->contains(fn ($count) => $count < 1)) {
            throw new CharacterAchievementMutationException(
                'The enabled criterion has an invalid zero required count.'
            );
        }
        if ($criterionCounts->count() > 1) {
            throw new CharacterAchievementMutationException(
                'The component has conflicting enabled criterion required counts.'
            );
        }

        return $criterionCounts->count() === 1
            ? min((int) $criterionCounts->first(), self::UINT32_MAX)
            : $this->normalizePresentationCount($presentationRequiredCount);
    }

    private function normalizePresentationCount(mixed $requiredCount): int
    {
        $requiredCount = (int) ($requiredCount ?? 1);

        return min(max($requiredCount, 1), self::UINT32_MAX);
    }

    private function withCharacterMutation(int $characterId, Closure $mutation): mixed
    {
        $usesAdvisoryLock = $this->connection->getDriverName() === 'mysql';
        $lockName = "eqemu_achievement_mutation_{$characterId}";
        $lockAcquired = false;

        try {
            if ($usesAdvisoryLock) {
                try {
                    $result = $this->connection->selectOne(
                        'SELECT GET_LOCK(?, 0) AS acquired',
                        [$lockName]
                    );
                } catch (Throwable $exception) {
                    throw new CharacterAchievementMutationException(
                        'The character achievement lock could not be acquired. Try again shortly.',
                        0,
                        $exception
                    );
                }
                $lockAcquired = (int) ($result->acquired ?? 0) === 1;
                if (! $lockAcquired) {
                    throw new CharacterAchievementMutationException(
                        'Achievement state is busy for this character. Try again after the active zone mutation finishes.'
                    );
                }
            }

            $this->connection->beginTransaction();
            try {
                $result = $mutation($this->connection);
                $this->connection->commit();

                return $result;
            } catch (Throwable $exception) {
                if ($this->connection->transactionLevel() > 0) {
                    $this->connection->rollBack();
                }

                throw $exception;
            }
        } finally {
            if ($lockAcquired) {
                try {
                    $this->connection->selectOne(
                        'SELECT RELEASE_LOCK(?) AS released',
                        [$lockName]
                    );
                } catch (Throwable $exception) {
                    report($exception);
                }
            }
        }
    }

    private function componentKey(int $achievementId, int $componentType, int $componentId): string
    {
        return "{$achievementId}:{$componentType}:{$componentId}";
    }

    private function rewardKey(int $achievementId, int $rewardId): string
    {
        return "{$achievementId}:{$rewardId}";
    }

    private function selectionKey(int $achievementId, int $rewardSetId): string
    {
        return "{$achievementId}:{$rewardSetId}";
    }

    private function optionKey(int $rewardSetId, int $optionId): string
    {
        return "{$rewardSetId}:{$optionId}";
    }
}
