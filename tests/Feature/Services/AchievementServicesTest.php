<?php

namespace Tests\Feature\Services;

use App\Exceptions\CharacterAchievementMutationException;
use App\Http\Middleware\EnsureAchievementSchema;
use App\Services\AchievementAggregateService;
use App\Services\AchievementSchemaGuard;
use App\Services\CharacterAchievementService;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
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
            'name' => 'Existing component',
            'description' => 'Existing description',
        ]);
        $this->connection->table('achievement_associations')->insert([
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
                ->table('achievement_associations')
                ->where('component_id', 500)
                ->value('required_count')
        );
    }

    public function test_editor_payload_preserves_reward_option_mappings(): void
    {
        $this->insertDefinition(100);
        $this->connection->table('rewards')->insert([
            [
                'reward_id' => 9001,
                'reward_type' => 2,
                'reward_data_id' => 0,
                'amount' => 1,
                'description' => 'Common AA point',
                'enabled' => 1,
            ],
            [
                'reward_id' => 9002,
                'reward_type' => 0,
                'reward_data_id' => 1001,
                'amount' => 1,
                'description' => 'Selectable item',
                'enabled' => 1,
            ],
            [
                'reward_id' => 9003,
                'reward_type' => 3,
                'reward_data_id' => 0,
                'amount' => 100,
                'description' => 'Automatic copper',
                'enabled' => 1,
            ],
            [
                'reward_id' => 9004,
                'reward_type' => 3,
                'reward_data_id' => 0,
                'amount' => 999,
                'description' => 'Task-only copper',
                'enabled' => 1,
            ],
        ]);
        $this->connection->table('reward_sets')->insert([
            'reward_set_id' => 8000,
            'title' => 'Choose one',
            'enabled' => 1,
        ]);
        $this->connection->table('reward_sources')->insert([
            'source_type' => 1,
            'source_id' => 100,
            'reward_set_id' => 8000,
            'enabled' => 0,
        ]);
        $this->connection->table('reward_source_entries')->insert([
            [
                'source_type' => 1,
                'source_id' => 100,
                'sequence' => 3,
                'reward_id' => 9003,
            ],
            [
                'source_type' => 2,
                'source_id' => 100,
                'sequence' => 4,
                'reward_id' => 9004,
            ],
        ]);
        $this->connection->table('reward_options')->insert([
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
        $this->connection->table('reward_option_entries')->insert([
            ['reward_set_id' => 8000, 'option_id' => 1, 'sequence' => 1, 'reward_id' => 9001],
            ['reward_set_id' => 8000, 'option_id' => 100, 'sequence' => 2, 'reward_id' => 9002],
        ]);

        $payload = (new AchievementAggregateService)->editorPayload(100);
        $mappings = collect($payload['rewards'])
            ->mapWithKeys(fn (array $reward) => [$reward['reward_id'] => $reward['option_id']])
            ->all();

        ksort($mappings);
        $this->assertSame([
            9001 => 1,
            9002 => 100,
            9003 => null,
        ], $mappings);
        $this->assertSame(1, $payload['reward_set']['enabled']);
        $this->assertSame(0, $payload['reward_set']['source_enabled']);
    }

    public function test_editor_payload_maps_final_component_name_and_description(): void
    {
        $this->insertDefinition(100);
        $this->connection->table('achievement_components')->insert([
            'achievement_id' => 100,
            'component_type' => 1,
            'sequence' => 5,
            'component_id' => 700,
            'name' => 'Visible component name',
            'description' => 'Detailed component description',
        ]);
        $this->connection->table('achievement_associations')->insert([
            'component_id' => 700,
            'required_count' => 3,
        ]);

        $component = (new AchievementAggregateService)->editorPayload(100)['components'][0];

        $this->assertSame('Visible component name', $component['name']);
        $this->assertSame('Detailed component description', $component['description']);
        $this->assertSame(3, $component['presentation_count']);
    }

    public function test_definition_ids_are_never_reused_from_preserved_character_state(): void
    {
        $this->insertCategory(1);
        $this->insertCharacter(42);
        $this->insertDefinition(10);
        $this->connection->table('character_achievements')->insert([
            'character_id' => 42,
            'achievement_id' => 11,
            'version' => 0,
            'completed_at' => 100,
        ]);
        $service = new AchievementAggregateService;

        try {
            $service->store($this->aggregatePayload(11, 501, 1));
            $this->fail('Expected preserved character state to reserve the achievement ID.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('preserved character state', $exception->errors()['id'][0]);
        }

        $this->assertSame(12, $service->editorPayload()['suggested_achievement_id']);
        $this->assertSame(12, $service->clone(10));
        $this->assertFalse($this->connection->table('achievements')->where('id', 11)->exists());
        $this->assertTrue($this->connection->table('achievements')->where('id', 12)->exists());
    }

    public function test_update_and_delete_preserve_shared_reward_catalog_rows(): void
    {
        $this->insertSharedRewardCatalog();
        $service = new AchievementAggregateService;
        $payload = $service->editorPayload(100);
        $payload['name'] = 'Updated without mutating shared grants';
        $payload['reward_set']['source_enabled'] = 0;

        $service->update(100, $payload);

        $this->assertSame(
            0,
            (int) $this->connection->table('reward_sources')
                ->where('source_type', 1)
                ->where('source_id', 100)
                ->value('enabled')
        );
        $this->assertSame(
            1,
            (int) $this->connection->table('reward_sets')
                ->where('reward_set_id', 8000)
                ->value('enabled')
        );

        $service->destroy(100);

        $this->assertSame(2, $this->connection->table('rewards')->count());
        $this->assertTrue($this->connection->table('reward_sets')->where('reward_set_id', 8000)->exists());
        $this->assertTrue($this->connection->table('reward_options')->where('reward_set_id', 8000)->exists());
        $this->assertTrue($this->connection->table('reward_option_entries')->where('reward_set_id', 8000)->exists());
        $this->assertTrue(
            $this->connection->table('reward_sources')
                ->where('source_type', 2)
                ->where('source_id', 200)
                ->where('reward_set_id', 8000)
                ->exists()
        );
        $this->assertTrue(
            $this->connection->table('reward_source_entries')
                ->where('source_type', 2)
                ->where('source_id', 200)
                ->where('reward_id', 9102)
                ->exists()
        );
        $this->assertFalse(
            $this->connection->table('reward_sources')
                ->where('source_type', 1)
                ->where('source_id', 100)
                ->exists()
        );
    }

    public function test_shared_reward_set_can_be_forked_without_mutating_other_sources(): void
    {
        $this->insertSharedRewardCatalog();
        $service = new AchievementAggregateService;
        $payload = $service->editorPayload(100);
        $payload['reward_set']['reward_set_id'] = 8001;
        $payload['reward_set']['title'] = 'Achievement-only fork';

        $service->update(100, $payload);

        $this->assertSame(
            8001,
            (int) $this->connection->table('reward_sources')
                ->where('source_type', 1)
                ->where('source_id', 100)
                ->value('reward_set_id')
        );
        $this->assertSame(
            8000,
            (int) $this->connection->table('reward_sources')
                ->where('source_type', 2)
                ->where('source_id', 200)
                ->value('reward_set_id')
        );
        $this->assertSame(
            'Shared set',
            $this->connection->table('reward_sets')->where('reward_set_id', 8000)->value('title')
        );
        $this->assertSame(
            'Achievement-only fork',
            $this->connection->table('reward_sets')->where('reward_set_id', 8001)->value('title')
        );
        $this->assertSame(1, $this->connection->table('reward_option_entries')->where('reward_set_id', 8000)->count());
        $this->assertSame(1, $this->connection->table('reward_option_entries')->where('reward_set_id', 8001)->count());
    }

    public function test_character_progress_uses_the_full_component_identity_and_criterion_count(): void
    {
        $this->insertCharacter(42);
        $this->insertDefinition(100, version: 3);
        $this->connection->table('achievement_components')->insert([
            [
                'achievement_id' => 100,
                'component_type' => 0,
                'sequence' => 2,
                'component_id' => 700,
                'name' => 'Type zero',
                'description' => 'Zero description',
            ],
            [
                'achievement_id' => 100,
                'component_type' => 1,
                'sequence' => 9,
                'component_id' => 700,
                'name' => 'Type one',
                'description' => 'One description',
            ],
        ]);
        $this->connection->table('achievement_associations')->insert([
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
        $this->assertSame(3, (int) $typeOneRow->version);

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

    public function test_version_zero_is_preserved_and_compared_exactly(): void
    {
        $this->insertCharacter(42);
        $this->insertDefinition(100, version: 0);
        $this->connection->table('achievement_components')->insert([
            'achievement_id' => 100,
            'component_type' => 1,
            'sequence' => 0,
            'component_id' => 700,
            'name' => 'Version-zero component',
            'description' => '',
        ]);
        $this->connection->table('achievement_associations')->insert([
            'component_id' => 700,
            'required_count' => 1,
        ]);
        $this->insertCriterion(100, 1, 700, 1);
        $this->connection->table('character_achievement_pending_updates')->insert([
            'update_id' => 77,
            'character_id' => 42,
            'source_target_type' => 0,
            'source_target_id' => 42,
            'operation' => 0,
            'achievement_id' => 100,
            'component_type' => 1,
            'component_id' => 700,
            'requested_value' => 1,
            'version' => 0,
            'status' => 1,
            'created_at' => 100,
        ]);
        $service = new CharacterAchievementService($this->app->make(DatabaseManager::class));

        $service->setExactProgress(42, 100, 1, 700, 1);
        $service->retryBlockedUpdate(42, 100, 77);

        $this->assertSame(
            0,
            (int) $this->connection->table('character_achievement_progress')->value('version')
        );
        $this->assertSame(
            0,
            (int) $this->connection->table('character_achievement_pending_updates')
                ->where('update_id', 77)
                ->value('status')
        );
        $catalog = $service->catalog(42, ['state' => 'all']);
        $this->assertFalse((bool) $catalog['achievements']->getCollection()->first()->has_version_mismatch);

        $this->connection->table('achievements')->where('id', 100)->update(['version' => 1]);
        $this->connection->table('character_achievement_pending_updates')
            ->where('update_id', 77)
            ->update(['status' => 1]);

        try {
            $service->retryBlockedUpdate(42, 100, 77);
            $this->fail('Expected a version-zero queued update to be rejected by version one.');
        } catch (CharacterAchievementMutationException $exception) {
            $this->assertStringContainsString('does not match', $exception->getMessage());
        }

        $catalog = $service->catalog(42, ['state' => 'all']);
        $this->assertTrue((bool) $catalog['achievements']->getCollection()->first()->has_version_mismatch);
    }

    public function test_selectable_rewards_retry_only_through_the_selection_ledger(): void
    {
        $this->insertCharacter(42);
        $this->insertSharedRewardCatalog();
        $this->connection->table('rewards')->insert([
            'reward_id' => 9103,
            'reward_type' => 2,
            'reward_data_id' => 0,
            'amount' => 1,
            'description' => 'Common AA grant',
            'enabled' => 1,
        ]);
        $this->connection->table('reward_options')->insert([
            'reward_set_id' => 8000,
            'option_id' => 2,
            'sequence' => 1,
            'label' => 'Common grants',
            'common_to_all' => 1,
            'flags' => 0,
            'enabled' => 1,
        ]);
        $this->connection->table('reward_option_entries')->insert([
            'reward_set_id' => 8000,
            'option_id' => 2,
            'sequence' => 0,
            'reward_id' => 9103,
        ]);
        $this->connection->table('character_achievement_reward_selections')->insert([
            'character_id' => 42,
            'achievement_id' => 100,
            'reward_set_id' => 8000,
            'selected_option_id' => 1,
            'status' => 3,
        ]);
        $this->connection->table('character_achievement_rewards')->insert([
            [
                'character_id' => 42,
                'achievement_id' => 100,
                'reward_id' => 9101,
                'status' => 0,
            ],
            [
                'character_id' => 42,
                'achievement_id' => 100,
                'reward_id' => 9102,
                'status' => 0,
            ],
            [
                'character_id' => 42,
                'achievement_id' => 100,
                'reward_id' => 9103,
                'status' => 0,
            ],
        ]);
        $service = new CharacterAchievementService($this->app->make(DatabaseManager::class));

        try {
            $service->markRewardRetryable(42, 100, 9101);
            $this->fail('Expected a selectable reward to reject individual retry.');
        } catch (CharacterAchievementMutationException $exception) {
            $this->assertStringContainsString('owning reward selection', $exception->getMessage());
        }

        $service->markSelectionRetryable(42, 100, 8000);

        $statuses = $this->connection->table('character_achievement_rewards')
            ->where('character_id', 42)
            ->where('achievement_id', 100)
            ->orderBy('reward_id')
            ->pluck('status', 'reward_id')
            ->mapWithKeys(fn ($status, $rewardId) => [(int) $rewardId => (int) $status])
            ->all();
        $this->assertSame([
            9101 => 2,
            9102 => 0,
            9103 => 2,
        ], $statuses);
        $this->assertSame(
            2,
            (int) $this->connection->table('character_achievement_reward_selections')
                ->where('character_id', 42)
                ->where('achievement_id', 100)
                ->where('reward_set_id', 8000)
                ->value('status')
        );

        $service->markRewardRetryable(42, 100, 9102);
        $this->assertSame(
            2,
            (int) $this->connection->table('character_achievement_rewards')
                ->where('reward_id', 9102)
                ->value('status')
        );

        $this->connection->table('rewards')->where('reward_id', 9102)->update([
            'amount' => '2147483648000',
        ]);
        $this->connection->table('character_achievement_rewards')
            ->where('reward_id', 9102)
            ->update(['status' => 0]);
        try {
            $service->markRewardRetryable(42, 100, 9102);
            $this->fail('Expected invalid delivery data to block an automatic reward retry.');
        } catch (CharacterAchievementMutationException $exception) {
            $this->assertStringContainsString('runtime will reject', $exception->getMessage());
        }
        $this->assertSame(
            0,
            (int) $this->connection->table('character_achievement_rewards')
                ->where('reward_id', 9102)
                ->value('status')
        );
    }

    public function test_online_character_blocks_direct_state_repairs(): void
    {
        $this->insertCharacter(42, inGame: 1);
        $this->insertDefinition(100);
        $this->connection->table('achievement_components')->insert([
            'achievement_id' => 100,
            'component_type' => 1,
            'sequence' => 0,
            'component_id' => 700,
            'name' => 'Offline-only progress',
            'description' => '',
        ]);
        $this->connection->table('achievement_associations')->insert([
            'component_id' => 700,
            'required_count' => 1,
        ]);
        $this->insertCriterion(100, 1, 700, 1);
        $service = new CharacterAchievementService($this->app->make(DatabaseManager::class));

        try {
            $service->setExactProgress(42, 100, 1, 700, 1);
            $this->fail('Expected an online character repair to be blocked.');
        } catch (CharacterAchievementMutationException $exception) {
            $this->assertStringContainsString('only while the character is offline', $exception->getMessage());
        }

        $this->assertFalse(
            $this->connection->table('character_achievement_progress')
                ->where('character_id', 42)
                ->exists()
        );
    }

    public function test_selection_retry_requires_both_source_and_set_to_be_enabled(): void
    {
        $this->insertCharacter(42);
        $this->insertSharedRewardCatalog();
        $this->connection->table('reward_sources')
            ->where('source_type', 1)
            ->where('source_id', 100)
            ->update(['enabled' => 0]);
        $this->connection->table('character_achievement_reward_selections')->insert([
            'character_id' => 42,
            'achievement_id' => 100,
            'reward_set_id' => 8000,
            'selected_option_id' => 1,
            'status' => 3,
        ]);
        $service = new CharacterAchievementService($this->app->make(DatabaseManager::class));

        try {
            $service->markSelectionRetryable(42, 100, 8000);
            $this->fail('Expected a disabled achievement reward source to block retry.');
        } catch (CharacterAchievementMutationException $exception) {
            $this->assertStringContainsString('source or reward set is missing or disabled', $exception->getMessage());
        }

        $this->assertSame(
            3,
            (int) $this->connection->table('character_achievement_reward_selections')
                ->where('character_id', 42)
                ->value('status')
        );
    }

    public function test_offline_state_lock_name_matches_the_zone_runtime_contract(): void
    {
        $this->assertSame(
            'eqemu_achievement_state_update_',
            CharacterAchievementService::STATE_UPDATE_LOCK_PREFIX
        );
    }

    public function test_schema_guard_accepts_final_schema_and_fails_closed_on_legacy_surface(): void
    {
        $guard = $this->app->make(AchievementSchemaGuard::class);
        $this->assertSame([], $guard->issues(true));

        $middleware = new EnsureAchievementSchema($guard);
        $request = Request::create('/achievements', 'GET', server: [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $passed = $middleware->handle(
            $request,
            static fn () => response('ready'),
            'state'
        );
        $this->assertSame(200, $passed->getStatusCode());

        $this->connection->getSchemaBuilder()->drop('reward_sources');

        $issues = $guard->issues(false);
        $this->assertContains('Missing table: reward_sources', $issues);

        $blocked = $middleware->handle(
            $request,
            static fn () => response('must not run'),
            'content'
        );
        $this->assertSame(503, $blocked->getStatusCode());
        $this->assertStringContainsString('9329', (string) $blocked->getContent());
        $this->assertStringNotContainsString('must not run', (string) $blocked->getContent());

        foreach (['achievements.create', 'achievements.store', 'achievements.clone'] as $routeName) {
            $route = $this->app['router']->getRoutes()->getByName($routeName);
            $this->assertNotNull($route);
            $this->assertContains('achievement.schema:state', $route->gatherMiddleware());
        }
        $priority = $this->app
            ->make(Kernel::class)
            ->getMiddlewarePriority();
        $guardIndex = array_search(EnsureAchievementSchema::class, $priority, true);
        $bindingIndex = array_search(SubstituteBindings::class, $priority, true);
        $this->assertIsInt($guardIndex);
        $this->assertIsInt($bindingIndex);
        $this->assertLessThan($bindingIndex, $guardIndex);
    }

    public function test_reset_preserves_reward_ledgers_unless_the_operator_explicitly_resets_them(): void
    {
        $this->insertCharacter(42);
        $this->insertDefinition(100);
        $this->insertCharacterState(42, 100);

        $service = new CharacterAchievementService(
            $this->app->make(DatabaseManager::class)
        );

        $withoutRewards = $service->reset(42, 100, false);
        $this->assertSame([
            'pending_updates' => 1,
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
                'character_achievement_pending_updates',
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
            'pending_updates' => 1,
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

    public function test_reset_can_clean_up_state_after_the_definition_is_deleted(): void
    {
        $this->insertCharacter(42);
        $this->insertDefinition(100);
        $this->insertCharacterState(42, 100);
        $this->connection->table('achievements')->where('id', 100)->delete();
        $service = new CharacterAchievementService($this->app->make(DatabaseManager::class));

        $deleted = $service->reset(42, 100, true);

        $this->assertSame(1, $deleted['completion']);
        $this->assertSame(1, $deleted['progress']);
        $this->assertSame(1, $deleted['pending_updates']);
        $this->assertSame(1, $deleted['reward_selections']);
        $this->assertSame(1, $deleted['rewards']);
    }

    public function test_processing_update_cannot_be_discarded_under_the_runtime_lease(): void
    {
        $this->insertCharacter(42);
        $this->insertDefinition(100);
        $this->connection->table('character_achievement_pending_updates')->insert([
            'update_id' => 77,
            'character_id' => 42,
            'source_target_type' => 0,
            'source_target_id' => 42,
            'operation' => 1,
            'achievement_id' => 100,
            'version' => 0,
            'status' => 2,
            'created_at' => 100,
        ]);
        $service = new CharacterAchievementService($this->app->make(DatabaseManager::class));

        try {
            $service->discardUpdate(42, 100, 77);
            $this->fail('Expected a runtime-owned processing update to reject discard.');
        } catch (CharacterAchievementMutationException $exception) {
            $this->assertStringContainsString('runtime lease', $exception->getMessage());
        }

        $this->assertTrue(
            $this->connection->table('character_achievement_pending_updates')
                ->where('update_id', 77)
                ->exists()
        );

        $this->connection->table('character_achievement_pending_updates')
            ->where('update_id', 77)
            ->update(['status' => 1]);
        $service->discardUpdate(42, 100, 77);
        $this->assertFalse(
            $this->connection->table('character_achievement_pending_updates')
                ->where('update_id', 77)
                ->exists()
        );
    }

    private function createSchema(): void
    {
        $schema = $this->connection->getSchemaBuilder();

        $schema->create('character_data', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('account_id')->default(0);
            $table->string('name')->default('');
            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedTinyInteger('class')->default(1);
            $table->unsignedTinyInteger('ingame')->default(0);
            $table->unsignedInteger('last_login')->default(0);
            $table->unsignedInteger('deleted_at')->nullable();
        });

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
            $table->boolean('has_reward')->default(false);
            $table->unsignedTinyInteger('client_flag')->default(0);
            $table->unsignedInteger('version')->default(0);
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
            $table->text('name')->default('');
            $table->text('description')->default('');
            $table->primary(['achievement_id', 'component_type', 'component_id']);
        });
        $schema->create('achievement_associations', function (Blueprint $table): void {
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
        $schema->create('rewards', function (Blueprint $table): void {
            $table->increments('reward_id');
            $table->unsignedTinyInteger('reward_type')->default(0);
            $table->unsignedInteger('reward_data_id')->default(0);
            $table->unsignedBigInteger('amount')->default(1);
            $table->string('description')->default('');
            $table->boolean('enabled')->default(true);
        });
        $schema->create('reward_sets', function (Blueprint $table): void {
            $table->unsignedInteger('reward_set_id')->primary();
            $table->string('title')->default('');
            $table->boolean('enabled')->default(true);
        });
        $schema->create('reward_options', function (Blueprint $table): void {
            $table->unsignedInteger('reward_set_id');
            $table->unsignedInteger('option_id');
            $table->unsignedInteger('sequence')->default(0);
            $table->string('label')->default('');
            $table->boolean('common_to_all')->default(false);
            $table->unsignedTinyInteger('flags')->default(0);
            $table->boolean('enabled')->default(true);
            $table->primary(['reward_set_id', 'option_id']);
        });
        $schema->create('reward_option_entries', function (Blueprint $table): void {
            $table->unsignedInteger('reward_set_id');
            $table->unsignedInteger('option_id');
            $table->unsignedInteger('sequence')->default(0);
            $table->unsignedInteger('reward_id');
            $table->primary(['reward_set_id', 'option_id', 'reward_id']);
            $table->unique(['reward_set_id', 'reward_id']);
        });
        $schema->create('reward_sources', function (Blueprint $table): void {
            $table->unsignedTinyInteger('source_type');
            $table->unsignedBigInteger('source_id');
            $table->unsignedInteger('reward_set_id');
            $table->boolean('enabled')->default(true);
            $table->primary(['source_type', 'source_id']);
        });
        $schema->create('reward_source_entries', function (Blueprint $table): void {
            $table->unsignedTinyInteger('source_type');
            $table->unsignedBigInteger('source_id');
            $table->unsignedInteger('sequence')->default(0);
            $table->unsignedInteger('reward_id');
            $table->primary(['source_type', 'source_id', 'reward_id']);
            $table->unique(['source_type', 'source_id', 'sequence']);
        });
        $schema->create('achievement_cast_requirements', function (Blueprint $table): void {
            $table->unsignedInteger('restriction_id');
            $table->unsignedInteger('achievement_id');
            $table->boolean('requires_completed')->default(true);
            $table->primary(['restriction_id', 'achievement_id']);
        });

        $schema->create('character_achievements', function (Blueprint $table): void {
            $table->unsignedInteger('character_id');
            $table->unsignedInteger('achievement_id');
            $table->unsignedInteger('version')->default(0);
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
            $table->unsignedInteger('version')->default(0);
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
            'character_achievement_pending_updates',
            function (Blueprint $table): void {
                $table->bigIncrements('update_id');
                $table->unsignedInteger('character_id');
                $table->unsignedTinyInteger('source_target_type')->default(0);
                $table->unsignedBigInteger('source_target_id')->default(0);
                $table->unsignedTinyInteger('operation')->default(0);
                $table->unsignedInteger('achievement_id');
                $table->unsignedTinyInteger('component_type')->default(0);
                $table->unsignedInteger('component_id')->default(0);
                $table->unsignedInteger('requested_value')->default(0);
                $table->unsignedInteger('version')->default(0);
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

    private function insertCharacter(int $characterId, int $inGame = 0): void
    {
        $this->connection->table('character_data')->insert([
            'id' => $characterId,
            'account_id' => 1,
            'name' => "Character {$characterId}",
            'level' => 60,
            'class' => 1,
            'ingame' => $inGame,
            'last_login' => 100,
            'deleted_at' => null,
        ]);
    }

    private function insertDefinition(
        int $achievementId,
        int $version = 0,
        int $enabled = 1
    ): void {
        $this->connection->table('achievements')->insert([
            'id' => $achievementId,
            'name' => "Achievement {$achievementId}",
            'description' => '',
            'icon_id' => 0,
            'points' => 0,
            'has_reward' => 0,
            'client_flag' => 0,
            'version' => $version,
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

    private function insertSharedRewardCatalog(): void
    {
        $this->insertDefinition(100);
        $this->connection->table('rewards')->insert([
            [
                'reward_id' => 9101,
                'reward_type' => 0,
                'reward_data_id' => 1001,
                'amount' => 1,
                'description' => 'Shared selectable item',
                'enabled' => 1,
            ],
            [
                'reward_id' => 9102,
                'reward_type' => 3,
                'reward_data_id' => 0,
                'amount' => 50,
                'description' => 'Shared automatic copper',
                'enabled' => 1,
            ],
        ]);
        $this->connection->table('reward_sets')->insert([
            'reward_set_id' => 8000,
            'title' => 'Shared set',
            'enabled' => 1,
        ]);
        $this->connection->table('reward_options')->insert([
            'reward_set_id' => 8000,
            'option_id' => 1,
            'sequence' => 0,
            'label' => 'Shared option',
            'common_to_all' => 0,
            'flags' => 0,
            'enabled' => 1,
        ]);
        $this->connection->table('reward_option_entries')->insert([
            'reward_set_id' => 8000,
            'option_id' => 1,
            'sequence' => 0,
            'reward_id' => 9101,
        ]);
        $this->connection->table('reward_sources')->insert([
            [
                'source_type' => 1,
                'source_id' => 100,
                'reward_set_id' => 8000,
                'enabled' => 1,
            ],
            [
                'source_type' => 2,
                'source_id' => 200,
                'reward_set_id' => 8000,
                'enabled' => 1,
            ],
        ]);
        $this->connection->table('reward_source_entries')->insert([
            [
                'source_type' => 1,
                'source_id' => 100,
                'sequence' => 0,
                'reward_id' => 9102,
            ],
            [
                'source_type' => 2,
                'source_id' => 200,
                'sequence' => 0,
                'reward_id' => 9102,
            ],
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
            'has_reward' => 0,
            'client_flag' => 0,
            'version' => 0,
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
                'name' => 'Component name',
                'description' => 'Component description',
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
            'version' => 0,
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
            'version' => 0,
            'updated_at' => 100,
        ]);
        $this->connection->table('character_achievement_pending_updates')->insert([
            'character_id' => $characterId,
            'source_target_type' => 0,
            'source_target_id' => $characterId,
            'operation' => 0,
            'achievement_id' => $achievementId,
            'component_type' => 1,
            'component_id' => 500,
            'requested_value' => 1,
            'version' => 0,
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
