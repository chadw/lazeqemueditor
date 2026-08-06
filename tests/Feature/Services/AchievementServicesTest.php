<?php

namespace Tests\Feature\Services;

use App\Services\AchievementAggregateService;
use App\Services\CharacterAchievementService;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AchievementServicesTest extends TestCase
{
    private ConnectionInterface $connection;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.eqemu', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]);
        DB::purge('eqemu');
        $this->connection = DB::connection('eqemu');
        $this->createSchema();
    }

    protected function tearDown(): void
    {
        DB::disconnect('eqemu');
        DB::purge('eqemu');

        parent::tearDown();
    }

    public function test_aggregate_store_rolls_back_when_a_shared_component_count_conflicts(): void
    {
        $this->insertCategory(1);
        $this->insertDefinition(10);
        $this->connection->table('achievement_components')->insert([
            'achievement_id' => 10,
            'component_type' => 0,
            'sequence' => 0,
            'component_id' => 500,
            'description' => 'Existing',
            'description_2' => '',
        ]);
        $this->connection->table('achievement_component_counts')->insert([
            'component_id' => 500,
            'required_count' => 5,
        ]);

        $service = new AchievementAggregateService;

        try {
            $service->store($this->aggregatePayload(20, 500, 7));
            $this->fail('Expected the shared component count conflict to abort the transaction.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'components.0.presentation_count',
                $exception->errors()
            );
        }

        $this->assertFalse(
            $this->connection->table('achievements')->where('id', 20)->exists()
        );
        $this->assertFalse(
            $this->connection
                ->table('achievement_category_associations')
                ->where('achievement_id', 20)
                ->exists()
        );
        $this->assertSame(
            5,
            (int) $this->connection
                ->table('achievement_component_counts')
                ->where('component_id', 500)
                ->value('required_count')
        );
    }

    public function test_editor_payload_preserves_reward_option_mappings(): void
    {
        $this->insertDefinition(100);
        $this->connection->table('achievement_rewards')->insert([
            [
                'reward_id' => 9001,
                'achievement_id' => 100,
                'sequence' => 1,
                'reward_type' => 2,
                'reward_data_id' => 0,
                'amount' => 1,
                'description' => 'Common AA point',
                'enabled' => 1,
            ],
            [
                'reward_id' => 9002,
                'achievement_id' => 100,
                'sequence' => 2,
                'reward_type' => 0,
                'reward_data_id' => 1001,
                'amount' => 1,
                'description' => 'Selectable item',
                'enabled' => 1,
            ],
            [
                'reward_id' => 9003,
                'achievement_id' => 100,
                'sequence' => 3,
                'reward_type' => 3,
                'reward_data_id' => 0,
                'amount' => 100,
                'description' => 'Automatic copper',
                'enabled' => 1,
            ],
        ]);
        $this->connection->table('achievement_reward_sets')->insert([
            'reward_set_id' => 8000,
            'achievement_id' => 100,
            'title' => 'Choose one',
            'enabled' => 1,
        ]);
        $this->connection->table('achievement_reward_options')->insert([
            [
                'reward_set_id' => 8000,
                'option_id' => 1,
                'sequence' => 1,
                'label' => 'Included with every choice',
                'common_to_all' => 1,
                'flags' => 0,
                'enabled' => 1,
            ],
            [
                'reward_set_id' => 8000,
                'option_id' => 100,
                'sequence' => 100,
                'label' => 'Item choice',
                'common_to_all' => 0,
                'flags' => 0,
                'enabled' => 1,
            ],
        ]);
        $this->connection->table('achievement_reward_option_entries')->insert([
            ['reward_set_id' => 8000, 'option_id' => 1, 'reward_id' => 9001],
            ['reward_set_id' => 8000, 'option_id' => 100, 'reward_id' => 9002],
        ]);

        $payload = (new AchievementAggregateService)->editorPayload(100);
        $mappings = collect($payload['rewards'])
            ->mapWithKeys(fn (array $reward) => [$reward['reward_id'] => $reward['option_id']])
            ->all();

        $this->assertSame([
            9001 => 1,
            9002 => 100,
            9003 => null,
        ], $mappings);
    }

    public function test_character_progress_uses_the_full_component_identity_and_criterion_count(): void
    {
        $this->insertDefinition(100, definitionVersion: 3);
        $this->connection->table('achievement_components')->insert([
            [
                'achievement_id' => 100,
                'component_type' => 0,
                'sequence' => 2,
                'component_id' => 700,
                'description' => 'Type zero',
                'description_2' => '',
            ],
            [
                'achievement_id' => 100,
                'component_type' => 1,
                'sequence' => 9,
                'component_id' => 700,
                'description' => 'Type one',
                'description_2' => '',
            ],
        ]);
        $this->connection->table('achievement_component_counts')->insert([
            'component_id' => 700,
            'required_count' => 10,
        ]);
        $this->insertCriterion(100, 0, 700, 7);
        $this->insertCriterion(100, 1, 700, 4);

        $service = new CharacterAchievementService(
            $this->app->make(DatabaseManager::class)
        );

        $typeOne = $service->setExactProgress(42, 100, 1, 700, 99);
        $this->assertSame([
            'requested_count' => 99,
            'current_count' => 4,
            'required_count' => 4,
            'completed' => true,
        ], $typeOne);

        $typeOneRow = $this->connection
            ->table('character_achievement_progress')
            ->where('character_id', 42)
            ->where('achievement_id', 100)
            ->where('component_type', 1)
            ->where('component_id', 700)
            ->first();
        $this->assertNotNull($typeOneRow);
        $this->assertSame(9, (int) $typeOneRow->component_sequence);
        $this->assertSame(4, (int) $typeOneRow->current_count);
        $this->assertSame(3, (int) $typeOneRow->definition_version);

        $typeZero = $service->setExactProgress(42, 100, 0, 700, 3);
        $this->assertSame(7, $typeZero['required_count']);
        $this->assertSame(3, $typeZero['current_count']);
        $this->assertFalse($typeZero['completed']);

        $this->assertSame(
            2,
            $this->connection
                ->table('character_achievement_progress')
                ->where('character_id', 42)
                ->where('achievement_id', 100)
                ->where('component_id', 700)
                ->count()
        );
    }

    public function test_reset_preserves_reward_ledgers_unless_the_operator_explicitly_resets_them(): void
    {
        $this->insertDefinition(100);
        $this->insertCharacterState(42, 100);

        $service = new CharacterAchievementService(
            $this->app->make(DatabaseManager::class)
        );

        $withoutRewards = $service->reset(42, 100, false);
        $this->assertSame([
            'pending_mutations' => 1,
            'progress' => 1,
            'completion' => 1,
            'reward_selections' => 0,
            'rewards' => 0,
        ], $withoutRewards);
        $this->assertFalse($this->characterStateExists('character_achievements', 42, 100));
        $this->assertFalse(
            $this->characterStateExists('character_achievement_progress', 42, 100)
        );
        $this->assertFalse(
            $this->characterStateExists(
                'character_achievement_pending_mutations',
                42,
                100
            )
        );
        $this->assertTrue(
            $this->characterStateExists('character_achievement_rewards', 42, 100)
        );
        $this->assertTrue(
            $this->characterStateExists(
                'character_achievement_reward_selections',
                42,
                100
            )
        );

        $this->insertResettableCharacterState(42, 100);
        $withRewards = $service->reset(42, 100, true);
        $this->assertSame([
            'pending_mutations' => 1,
            'progress' => 1,
            'completion' => 1,
            'reward_selections' => 1,
            'rewards' => 1,
        ], $withRewards);
        $this->assertFalse(
            $this->characterStateExists('character_achievement_rewards', 42, 100)
        );
        $this->assertFalse(
            $this->characterStateExists(
                'character_achievement_reward_selections',
                42,
                100
            )
        );
    }

    private function createSchema(): void
    {
        $schema = $this->connection->getSchemaBuilder();

        $schema->create('achievement_categories', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('parent_id')->default(0);
            $table->unsignedInteger('sequence')->default(0);
            $table->string('name')->default('');
            $table->text('description')->default('');
            $table->string('icon')->default('');
        });
        $schema->create('achievements', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('name')->default('');
            $table->text('description')->default('');
            $table->unsignedInteger('icon_id')->default(0);
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('reward_display')->default(0);
            $table->unsignedTinyInteger('world_display_flag')->default(0);
            $table->unsignedInteger('definition_version')->default(1);
            $table->boolean('reset_on_version_change')->default(true);
            $table->boolean('enabled')->default(true);
        });
        $schema->create('achievement_category_associations', function (Blueprint $table): void {
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('sequence')->default(0);
            $table->unsignedInteger('achievement_id');
            $table->string('display_text')->default('');
            $table->primary(['category_id', 'achievement_id']);
        });
        $schema->create('achievement_components', function (Blueprint $table): void {
            $table->unsignedInteger('achievement_id');
            $table->unsignedTinyInteger('component_type');
            $table->unsignedInteger('sequence')->default(0);
            $table->unsignedInteger('component_id');
            $table->text('description')->default('');
            $table->text('description_2')->default('');
            $table->primary(['achievement_id', 'component_type', 'component_id']);
        });
        $schema->create('achievement_component_counts', function (Blueprint $table): void {
            $table->unsignedInteger('component_id')->primary();
            $table->unsignedInteger('required_count')->default(1);
        });
        $schema->create('achievement_criteria', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedInteger('achievement_id');
            $table->unsignedTinyInteger('component_type');
            $table->unsignedInteger('component_sequence')->default(0);
            $table->unsignedInteger('component_id');
            $table->unsignedTinyInteger('event_type')->default(0);
            $table->unsignedTinyInteger('progress_mode')->default(0);
            $table->unsignedTinyInteger('behavior')->default(0);
            $table->unsignedInteger('target_id')->default(0);
            $table->unsignedInteger('target_id2')->default(0);
            $table->bigInteger('target_value')->default(0);
            $table->unsignedInteger('required_count')->default(1);
            $table->boolean('enabled')->default(true);
        });
        $schema->create('achievement_rewards', function (Blueprint $table): void {
            $table->bigIncrements('reward_id');
            $table->unsignedInteger('achievement_id');
            $table->unsignedInteger('sequence')->default(0);
            $table->unsignedTinyInteger('reward_type')->default(0);
            $table->unsignedInteger('reward_data_id')->default(0);
            $table->unsignedBigInteger('amount')->default(1);
            $table->string('description')->default('');
            $table->boolean('enabled')->default(true);
            $table->unique(['achievement_id', 'sequence']);
        });
        $schema->create('achievement_reward_sets', function (Blueprint $table): void {
            $table->unsignedInteger('reward_set_id')->primary();
            $table->unsignedInteger('achievement_id')->unique();
            $table->string('title')->default('');
            $table->boolean('enabled')->default(true);
        });
        $schema->create('achievement_reward_options', function (Blueprint $table): void {
            $table->unsignedInteger('reward_set_id');
            $table->unsignedInteger('option_id');
            $table->unsignedInteger('sequence')->default(0);
            $table->string('label')->default('');
            $table->boolean('common_to_all')->default(false);
            $table->unsignedTinyInteger('flags')->default(0);
            $table->boolean('enabled')->default(true);
            $table->primary(['reward_set_id', 'option_id']);
        });
        $schema->create('achievement_reward_option_entries', function (Blueprint $table): void {
            $table->unsignedInteger('reward_set_id');
            $table->unsignedInteger('option_id');
            $table->unsignedBigInteger('reward_id')->unique();
            $table->primary(['reward_set_id', 'option_id', 'reward_id']);
        });
        $schema->create('achievement_cast_restrictions', function (Blueprint $table): void {
            $table->unsignedInteger('restriction_id');
            $table->unsignedInteger('achievement_id');
            $table->boolean('requires_completed')->default(true);
            $table->primary(['restriction_id', 'achievement_id']);
        });

        $schema->create('character_achievements', function (Blueprint $table): void {
            $table->unsignedInteger('character_id');
            $table->unsignedInteger('achievement_id');
            $table->unsignedInteger('definition_version')->default(1);
            $table->unsignedInteger('completed_at')->default(0);
            $table->primary(['character_id', 'achievement_id']);
        });
        $schema->create('character_achievement_progress', function (Blueprint $table): void {
            $table->unsignedInteger('character_id');
            $table->unsignedInteger('achievement_id');
            $table->unsignedTinyInteger('component_type');
            $table->unsignedInteger('component_sequence')->default(0);
            $table->unsignedInteger('component_id');
            $table->unsignedBigInteger('current_count')->default(0);
            $table->boolean('completed')->default(false);
            $table->unsignedInteger('definition_version')->default(1);
            $table->unsignedInteger('updated_at')->default(0);
            $table->primary([
                'character_id',
                'achievement_id',
                'component_type',
                'component_id',
            ]);
        });
        $schema->create('character_achievement_rewards', function (Blueprint $table): void {
            $table->unsignedInteger('character_id');
            $table->unsignedInteger('achievement_id');
            $table->unsignedBigInteger('reward_id');
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedInteger('attempt_count')->default(0);
            $table->unsignedInteger('granted_at')->default(0);
            $table->unsignedInteger('last_attempt_at')->default(0);
            $table->string('last_error')->default('');
            $table->primary(['character_id', 'achievement_id', 'reward_id']);
        });
        $schema->create(
            'character_achievement_reward_selections',
            function (Blueprint $table): void {
                $table->unsignedInteger('character_id');
                $table->unsignedInteger('achievement_id');
                $table->unsignedInteger('reward_set_id');
                $table->unsignedInteger('selected_option_id')->default(0);
                $table->unsignedTinyInteger('status')->default(0);
                $table->unsignedInteger('attempt_count')->default(0);
                $table->unsignedInteger('claimed_at')->default(0);
                $table->unsignedInteger('last_attempt_at')->default(0);
                $table->string('last_error')->default('');
                $table->primary(['character_id', 'achievement_id', 'reward_set_id']);
            }
        );
        $schema->create(
            'character_achievement_pending_mutations',
            function (Blueprint $table): void {
                $table->bigIncrements('mutation_id');
                $table->unsignedInteger('character_id');
                $table->unsignedTinyInteger('source_target_type')->default(0);
                $table->unsignedBigInteger('source_target_id')->default(0);
                $table->unsignedTinyInteger('operation')->default(0);
                $table->unsignedInteger('achievement_id');
                $table->unsignedTinyInteger('component_type')->default(0);
                $table->unsignedInteger('component_id')->default(0);
                $table->unsignedInteger('requested_value')->default(0);
                $table->unsignedInteger('definition_version')->default(0);
                $table->unsignedTinyInteger('status')->default(0);
                $table->unsignedInteger('attempt_count')->default(0);
                $table->unsignedInteger('created_at')->default(0);
                $table->unsignedInteger('last_attempt_at')->default(0);
                $table->string('last_error')->default('');
            }
        );
    }

    private function insertCategory(int $categoryId): void
    {
        $this->connection->table('achievement_categories')->insert([
            'id' => $categoryId,
            'parent_id' => 0,
            'sequence' => 0,
            'name' => 'General',
            'description' => '',
            'icon' => '',
        ]);
    }

    private function insertDefinition(
        int $achievementId,
        int $definitionVersion = 1,
        int $enabled = 1
    ): void {
        $this->connection->table('achievements')->insert([
            'id' => $achievementId,
            'name' => "Achievement {$achievementId}",
            'description' => '',
            'icon_id' => 0,
            'points' => 0,
            'reward_display' => 0,
            'world_display_flag' => 0,
            'definition_version' => $definitionVersion,
            'reset_on_version_change' => 1,
            'enabled' => $enabled,
        ]);
    }

    private function insertCriterion(
        int $achievementId,
        int $componentType,
        int $componentId,
        int $requiredCount
    ): void {
        $this->connection->table('achievement_criteria')->insert([
            'achievement_id' => $achievementId,
            'component_type' => $componentType,
            'component_sequence' => 0,
            'component_id' => $componentId,
            'event_type' => 0,
            'progress_mode' => 0,
            'behavior' => 0,
            'target_id' => 0,
            'target_id2' => 0,
            'target_value' => 0,
            'required_count' => $requiredCount,
            'enabled' => 1,
        ]);
    }

    private function aggregatePayload(
        int $achievementId,
        int $componentId,
        int $presentationCount
    ): array {
        return [
            'id' => $achievementId,
            'name' => "Achievement {$achievementId}",
            'description' => '',
            'icon_id' => 0,
            'points' => 0,
            'reward_display' => 0,
            'world_display_flag' => 0,
            'definition_version' => 1,
            'reset_on_version_change' => 1,
            'enabled' => 1,
            'associations' => [[
                'category_id' => 1,
                'sequence' => 0,
                'display_text' => '',
            ]],
            'components' => [[
                'component_type' => 1,
                'sequence' => 0,
                'component_id' => $componentId,
                'description' => '',
                'description_2' => '',
                'presentation_count' => $presentationCount,
                'criteria' => [],
            ]],
            'rewards' => [],
            'reward_set' => null,
            'restrictions' => [],
        ];
    }

    private function insertCharacterState(int $characterId, int $achievementId): void
    {
        $this->insertResettableCharacterState($characterId, $achievementId);

        $this->connection->table('character_achievement_rewards')->insert([
            'character_id' => $characterId,
            'achievement_id' => $achievementId,
            'reward_id' => 900,
            'status' => 1,
        ]);
        $this->connection->table('character_achievement_reward_selections')->insert([
            'character_id' => $characterId,
            'achievement_id' => $achievementId,
            'reward_set_id' => 800,
            'selected_option_id' => 1,
            'status' => 1,
        ]);
    }

    private function insertResettableCharacterState(
        int $characterId,
        int $achievementId
    ): void {
        $this->connection->table('character_achievements')->insert([
            'character_id' => $characterId,
            'achievement_id' => $achievementId,
            'definition_version' => 1,
            'completed_at' => 100,
        ]);
        $this->connection->table('character_achievement_progress')->insert([
            'character_id' => $characterId,
            'achievement_id' => $achievementId,
            'component_type' => 1,
            'component_sequence' => 0,
            'component_id' => 500,
            'current_count' => 1,
            'completed' => 1,
            'definition_version' => 1,
            'updated_at' => 100,
        ]);
        $this->connection->table('character_achievement_pending_mutations')->insert([
            'character_id' => $characterId,
            'source_target_type' => 0,
            'source_target_id' => $characterId,
            'operation' => 0,
            'achievement_id' => $achievementId,
            'component_type' => 1,
            'component_id' => 500,
            'requested_value' => 1,
            'definition_version' => 1,
            'status' => 0,
            'created_at' => 100,
        ]);
    }

    private function characterStateExists(
        string $table,
        int $characterId,
        int $achievementId
    ): bool {
        return $this->connection
            ->table($table)
            ->where('character_id', $characterId)
            ->where('achievement_id', $achievementId)
            ->exists();
    }
}
