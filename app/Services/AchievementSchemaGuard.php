<?php

namespace App\Services;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Throwable;

class AchievementSchemaGuard
{
    /**
     * The minimum final-schema surface used by the definition editor.
     *
     * @var array<string, list<string>>
     */
    private const CONTENT_REQUIREMENTS = [
        'achievement_categories' => [
            'id', 'parent_id', 'sequence', 'name', 'description', 'icon',
        ],
        'achievements' => [
            'id', 'name', 'description', 'icon_id', 'points', 'has_reward', 'client_flag', 'version',
            'reset_on_version_change', 'enabled',
        ],
        'achievement_category_associations' => [
            'category_id', 'sequence', 'achievement_id', 'display_text',
        ],
        'achievement_components' => [
            'achievement_id', 'component_type', 'sequence', 'component_id', 'name', 'description',
        ],
        'achievement_associations' => ['component_id', 'required_count'],
        'achievement_criteria' => [
            'id', 'achievement_id', 'component_type', 'component_sequence', 'component_id',
            'event_type', 'progress_mode', 'behavior', 'target_id', 'target_id2',
            'target_value', 'required_count', 'enabled',
        ],
        'achievement_cast_requirements' => [
            'restriction_id', 'achievement_id', 'requires_completed',
        ],
        'rewards' => [
            'reward_id', 'reward_type', 'reward_data_id', 'amount', 'description', 'enabled',
        ],
        'reward_sets' => ['reward_set_id', 'title', 'enabled'],
        'reward_options' => [
            'reward_set_id', 'option_id', 'sequence', 'label', 'common_to_all', 'flags', 'enabled',
        ],
        'reward_option_entries' => [
            'reward_set_id', 'option_id', 'sequence', 'reward_id',
        ],
        'reward_sources' => ['source_type', 'source_id', 'reward_set_id', 'enabled'],
        'reward_source_entries' => ['source_type', 'source_id', 'sequence', 'reward_id'],
    ];

    /**
     * The minimum final-schema surface used by character achievement tools.
     *
     * @var array<string, list<string>>
     */
    private const STATE_REQUIREMENTS = [
        'character_data' => [
            'id', 'account_id', 'name', 'level', 'class', 'ingame', 'last_login', 'deleted_at',
        ],
        'character_achievements' => [
            'character_id', 'achievement_id', 'version', 'completed_at',
        ],
        'character_achievement_progress' => [
            'character_id', 'achievement_id', 'component_type', 'component_sequence',
            'component_id', 'current_count', 'completed', 'version', 'updated_at',
        ],
        'character_achievement_rewards' => [
            'character_id', 'achievement_id', 'reward_id', 'status', 'attempt_count',
            'granted_at', 'last_attempt_at', 'last_error',
        ],
        'character_achievement_reward_selections' => [
            'character_id', 'achievement_id', 'reward_set_id', 'selected_option_id',
            'status', 'attempt_count', 'claimed_at', 'last_attempt_at', 'last_error',
        ],
        'character_achievement_pending_updates' => [
            'update_id', 'character_id', 'source_target_type', 'source_target_id', 'operation',
            'achievement_id', 'component_type', 'component_id', 'requested_value', 'version',
            'status', 'attempt_count', 'created_at', 'last_attempt_at', 'last_error',
        ],
    ];

    public function __construct(private readonly DatabaseManager $database) {}

    /**
     * Return actionable final-schema problems without issuing an editor query.
     *
     * @return list<string>
     */
    public function issues(bool $includeCharacterState = false): array
    {
        try {
            $connection = $this->database->connection('eqemu');
            $requirements = self::CONTENT_REQUIREMENTS;
            if ($includeCharacterState) {
                $requirements = array_merge($requirements, self::STATE_REQUIREMENTS);
            }

            return $this->inspect($connection, $requirements);
        } catch (Throwable $exception) {
            report($exception);

            return [
                'The EQEmu database schema could not be inspected. Verify the eqemu connection and database permissions.',
            ];
        }
    }

    /**
     * @param  array<string, list<string>>  $requirements
     * @return list<string>
     */
    private function inspect(ConnectionInterface $connection, array $requirements): array
    {
        $schema = $connection->getSchemaBuilder();
        $issues = [];

        foreach ($requirements as $table => $columns) {
            if (! $schema->hasTable($table)) {
                $issues[] = "Missing table: {$table}";

                continue;
            }

            $missingColumns = array_values(array_filter(
                $columns,
                static fn (string $column): bool => ! $schema->hasColumn($table, $column)
            ));
            if ($missingColumns !== []) {
                $issues[] = "Table {$table} is missing: ".implode(', ', $missingColumns);
            }
        }

        return $issues;
    }
}
