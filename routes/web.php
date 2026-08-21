<?php

use App\Http\Controllers\AaAbilityController;
use App\Http\Controllers\AaRankController;
use App\Http\Controllers\AaRankEffectController;
use App\Http\Controllers\AaRankPrereqController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AchievementCategoryController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AchievementLookupController;
use App\Http\Controllers\AltCurrencyController;
use App\Http\Controllers\AuraController;
use App\Http\Controllers\BaseDataController;
use App\Http\Controllers\BeastlordPetController;
use App\Http\Controllers\BlockedSpellController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CharacterAchievementController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterExpeditionLockoutController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClientFileController;
use App\Http\Controllers\ContentFlagController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataBucketController;
use App\Http\Controllers\DbstrController;
use App\Http\Controllers\DiscordWebhookController;
use App\Http\Controllers\DoorController;
use App\Http\Controllers\DynamicZoneController;
use App\Http\Controllers\DynamicZoneLockoutController;
use App\Http\Controllers\DynamicZoneMemberController;
use App\Http\Controllers\DynamicZoneTemplateController;
use App\Http\Controllers\FactionAssociationController;
use App\Http\Controllers\FactionController;
use App\Http\Controllers\FactionListModController;
use App\Http\Controllers\FactionValueController;
use App\Http\Controllers\FishingController;
use App\Http\Controllers\ForageController;
use App\Http\Controllers\GlobalLootController;
use App\Http\Controllers\GraveyardController;
use App\Http\Controllers\GridController;
use App\Http\Controllers\GridEntryController;
use App\Http\Controllers\GroundSpawnController;
use App\Http\Controllers\GuildController;
use App\Http\Controllers\IdPickerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemEvolvingDetailController;
use App\Http\Controllers\LdonTrapEntryController;
use App\Http\Controllers\LdonTrapTemplateController;
use App\Http\Controllers\LootDropController;
use App\Http\Controllers\LootDropEntryController;
use App\Http\Controllers\LootTableController;
use App\Http\Controllers\LootTableEntryController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MerchantTempController;
use App\Http\Controllers\MountController;
use App\Http\Controllers\NpcEmoteController;
use App\Http\Controllers\NpcFactionController;
use App\Http\Controllers\NpcFactionEntryController;
use App\Http\Controllers\NpcSpellController;
use App\Http\Controllers\NpcSpellEffectController;
use App\Http\Controllers\NpcSpellEffectEntryController;
use App\Http\Controllers\NpcSpellEntryController;
use App\Http\Controllers\NpcTypeController;
use App\Http\Controllers\ObjectController;
use App\Http\Controllers\ParcelController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetEquipmentController;
use App\Http\Controllers\PetEquipmentEntryController;
use App\Http\Controllers\PlayerLogController;
use App\Http\Controllers\PlayerLogSettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestGlobalController;
use App\Http\Controllers\ServerRuleController;
use App\Http\Controllers\SharedTaskController;
use App\Http\Controllers\SpawnEntryController;
use App\Http\Controllers\SpawnGroupController;
use App\Http\Controllers\SpawnPointController;
use App\Http\Controllers\SpellController;
use App\Http\Controllers\StartingItemController;
use App\Http\Controllers\TaskActivityController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\TraderAuditController;
use App\Http\Controllers\TradeskillContainerTemplateController;
use App\Http\Controllers\TradeskillRecipeController;
use App\Http\Controllers\TradeskillRecipeEntryController;
use App\Http\Controllers\TrapController;
use App\Http\Controllers\TributeController;
use App\Http\Controllers\TributeLevelController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\VariableController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ZonePointController;
use App\Http\Middleware\NoCache;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::prefix('dashboard')->name('dashboard.')->middleware('verified')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/changelog', [DashboardController::class, 'changelog'])->name('changelog');
    });

    // NPC helper endpoints
    Route::get('/api/npcs', [NpcTypeController::class, 'apiIndex'])->name('api.npcs.index');
    Route::get('/api/zones/{zoneid}/versions', [NpcTypeController::class, 'zoneVersions'])->name('api.zones.versions');

    // aa abilitys/ranks
    Route::prefix('aa')->name('aa.')->group(function () {
        Route::get('/', [AaAbilityController::class, 'index'])
            ->middleware(NoCache::class)
            ->name('index');
        Route::get('/create', [AaAbilityController::class, 'create'])->name('create');
        Route::get('/search', [AaAbilityController::class, 'search'])->name('search');
        Route::get('{ability}/edit', [AaAbilityController::class, 'edit'])->name('edit');
        Route::post('{ability}/clone', [AaAbilityController::class, 'clone'])->name('clone');
        Route::post('{ability}/ranks', [AaRankController::class, 'store'])->name('ability.ranks.store');
        Route::post('/', [AaAbilityController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{ability}', [AaAbilityController::class, 'update'])->name('update');
        Route::delete('/{ability}', [AaAbilityController::class, 'destroy'])->name('destroy');

        Route::resource('ranks', AaRankController::class)->except(['index', 'show']);

        Route::prefix('ranks/{rank}/effects')->name('ranks.effects.')->group(function () {
            Route::post('/', [AaRankEffectController::class, 'store'])->name('store');
            Route::put('{slot}', [AaRankEffectController::class, 'update'])->name('update');
            Route::delete('{slot}', [AaRankEffectController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('ranks/{rank}/prereqs')->name('ranks.prereqs.')->group(function () {
            Route::post('/', [AaRankPrereqController::class, 'store'])->name('store');
            Route::put('{aaId}', [AaRankPrereqController::class, 'update'])->name('update');
            Route::delete('{aaId}', [AaRankPrereqController::class, 'destroy'])->name('destroy');
        });

        // save one rank
        Route::post('ranks/{rank}/batch-save', [AaRankController::class, 'batchSave'])->name('ranks.batch-save');
        // save all ranks
        Route::post('ranks/batch-save-multiple', [AaRankController::class, 'batchSaveMultiple'])->name('ranks.batch-save-multiple');
    });

    // Achievement definitions and authored category tree
    Route::prefix('achievements')->name('achievements.')->group(function () {
        Route::get('/', [AchievementController::class, 'index'])->name('index');
        Route::get('/create', [AchievementController::class, 'create'])->name('create');
        Route::get('/lookups/{type}', AchievementLookupController::class)->name('lookups');
        Route::post('/', [AchievementController::class, 'store'])->name('store');
        Route::get('/{achievement}/edit', [AchievementController::class, 'edit'])->name('edit')->whereNumber('achievement');
        Route::post('/{achievement}/clone', [AchievementController::class, 'clone'])
            ->name('clone')->whereNumber('achievement');
        Route::match(['put', 'patch'], '/{achievement}', [AchievementController::class, 'update'])->name('update')->whereNumber('achievement');
        Route::delete('/{achievement}', [AchievementController::class, 'destroy'])->name('destroy')->whereNumber('achievement');
    });

    Route::prefix('achievement-categories')->name('achievement-categories.')->group(function () {
        Route::get('/', [AchievementCategoryController::class, 'index'])->name('index');
        Route::post('/', [AchievementCategoryController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '/{achievement_category}', [AchievementCategoryController::class, 'update'])
            ->name('update')->whereNumber('achievement_category');
        Route::delete('/{achievement_category}', [AchievementCategoryController::class, 'destroy'])
            ->name('destroy')->whereNumber('achievement_category');
    });

    Route::get('/character-achievements', [CharacterAchievementController::class, 'index'])
        ->name('character-achievements.index');

    // accounts
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/search', [AccountController::class, 'search'])->name('search');
        Route::get('/show/{account}', [AccountController::class, 'show'])->name('show');
        Route::get('{account}/ips/{ip}/others', [AccountController::class, 'ipsFor'])->name('ips.others')->where('ip', '.*');
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{account}', [AccountController::class, 'update'])->name('update');
        Route::delete('/{account}', [AccountController::class, 'destroy'])->name('destroy');
    });

    // alt currency
    Route::prefix('alt-currency')->name('alt-currency.')->group(function () {
        Route::get('/', [AltCurrencyController::class, 'index'])->name('index');
        Route::post('/', [AltCurrencyController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{alternateCurrency}', [AltCurrencyController::class, 'update'])->name('update');
        Route::delete('/{alternateCurrency}', [AltCurrencyController::class, 'destroy'])->name('destroy');

        // alt currency npcs
        Route::prefix('npcs')->name('npcs.')->group(function () {
            Route::get('/', [AltCurrencyController::class, 'npcs'])->name('index');
            Route::post('/', [AltCurrencyController::class, 'storeNpc'])->name('store');
            Route::match(['put', 'patch'], '{npc}', [AltCurrencyController::class, 'updateNpc'])->name('update');
            Route::delete('{npc}', [AltCurrencyController::class, 'destroyNpc'])->name('destroy');
        });

        // alt currency characters
        Route::prefix('characters')->name('characters.')->group(function () {
            Route::get('/', [AltCurrencyController::class, 'characters'])->name('index');
            Route::post('/', [AltCurrencyController::class, 'storeCharacter'])->name('store');
            Route::match(['put', 'patch'], '{character}', [AltCurrencyController::class, 'updateCharacter'])->name('update');
            Route::delete('{char_id}/{currency_id}', [AltCurrencyController::class, 'destroyCharacter'])->name('destroy');
        });
    });

    // auras
    Route::resource('auras', AuraController::class);

    Route::prefix('beastlord-pets')->name('beastlord-pets.')->group(function () {
        Route::get('/', [BeastlordPetController::class, 'index'])->name('index');
        Route::post('/', [BeastlordPetController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{pet}', [BeastlordPetController::class, 'update'])->name('update');
        Route::delete('{pet}', [BeastlordPetController::class, 'destroy'])->name('destroy');
    });

    // books
    Route::resource('books', BookController::class);

    // character
    Route::prefix('characters')->name('characters.')->group(function () {
        Route::get('/', [CharacterController::class, 'index'])->name('index');
        Route::get('/search', [CharacterController::class, 'search'])->name('search');
        Route::get('/recipes', [CharacterController::class, 'recipes'])->name('recipes');

        Route::prefix('base-data')->name('base-data.')->group(function () {
            Route::get('/', [BaseDataController::class, 'index'])->name('index');
            Route::post('/', [BaseDataController::class, 'store'])->name('store');

            Route::get('{level}/{class}/edit', [BaseDataController::class, 'edit'])->name('edit');
            Route::put('{level}/{class}', [BaseDataController::class, 'update'])->name('update');
            Route::delete('{level}/{class}', [BaseDataController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{character}/achievements')->name('achievements.')->group(function () {
            Route::get('/', [CharacterAchievementController::class, 'show'])->name('show')->whereNumber('character');
            Route::put('/{achievement}/components/{componentType}/{component}', [CharacterAchievementController::class, 'updateProgress'])
                ->name('progress.update')->whereNumber(['character', 'achievement', 'componentType', 'component']);
            Route::post('/{achievement}/complete', [CharacterAchievementController::class, 'forceComplete'])
                ->name('complete')->whereNumber(['character', 'achievement']);
            Route::delete('/{achievement}/reset', [CharacterAchievementController::class, 'reset'])
                ->name('reset')->whereNumber(['character', 'achievement']);
            Route::patch('/{achievement}/rewards/{reward}/retry', [CharacterAchievementController::class, 'markRewardRetryable'])
                ->name('rewards.retry')->whereNumber(['character', 'achievement', 'reward']);
            Route::patch('/{achievement}/reward-sets/{rewardSet}/retry', [CharacterAchievementController::class, 'markSelectionRetryable'])
                ->name('reward-selections.retry')->whereNumber(['character', 'achievement', 'rewardSet']);
            Route::patch('/{achievement}/updates/{update}/retry', [CharacterAchievementController::class, 'retryUpdate'])
                ->name('updates.retry')->whereNumber(['character', 'achievement', 'update']);
            Route::delete('/{achievement}/updates/{update}', [CharacterAchievementController::class, 'discardUpdate'])
                ->name('updates.discard')->whereNumber(['character', 'achievement', 'update']);
        });

        Route::get('{character}/edit', [CharacterController::class, 'edit'])->name('edit');
        Route::get('{character}', [CharacterController::class, 'show'])->name('show');
        Route::match(['put', 'patch'], '{character}', [CharacterController::class, 'update'])->name('update');
        Route::match(['put', 'patch'], '{character}/move', [CharacterController::class, 'move'])->name('move');
    });

    // chat channels
    Route::resource('chats', ChatController::class);

    // client files
    Route::get('/client-files', [ClientFileController::class, 'index'])->name('client-files.index');
    Route::get('/client-files/export', [ClientFileController::class, 'export'])->name('client-files.export');

    // content flags
    Route::resource('content-flags', ContentFlagController::class);

    // databuckets
    Route::resource('databuckets', DataBucketController::class);

    // dbstr
    Route::prefix('dbstr')->name('dbstr.')->group(function () {
        Route::get('/lookup', [DbstrController::class, 'lookup'])->name('lookup');
        Route::get('/search', [DbstrController::class, 'search'])->name('search');
        Route::get('/next-id', [DbstrController::class, 'nextId'])->name('next-id');
        Route::get('/', [DbstrController::class, 'index'])->name('index');
        Route::post('/', [DbstrController::class, 'store'])->name('store');
        Route::put('/{type}/{id}', [DbstrController::class, 'update'])->name('update');
        Route::delete('/{type}/{id}', [DbstrController::class, 'destroy'])->name('destroy');
    });

    // discord webhooks
    Route::resource('discord-webhooks', DiscordWebhookController::class)
        ->parameters(['discord-webhooks' => 'hook']);

    // dynamiczones
    Route::prefix('dynamiczones')->name('dynamiczones.')->group(function () {
        Route::get('/', [DynamicZoneController::class, 'index'])->name('index');
        Route::post('/', [DynamicZoneController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{dynamiczone}', [DynamicZoneController::class, 'update'])->name('update');
        Route::delete('{dynamiczone}', [DynamicZoneController::class, 'destroy'])->name('destroy');

        Route::resource('templates', DynamicZoneTemplateController::class)
            ->parameters(['templates' => 'template'])
            ->names('templates')
            ->except(['create', 'show', 'edit']);

        Route::resource('lockouts', DynamicZoneLockoutController::class)
            ->parameters(['lockouts' => 'lockout'])
            ->names('lockouts')
            ->except(['create', 'show', 'edit']);

        Route::delete('character-lockouts/bulk', [CharacterExpeditionLockoutController::class, 'bulkDestroy'])
            ->name('character-lockouts.bulk-destroy');
        Route::resource('character-lockouts', CharacterExpeditionLockoutController::class)
            ->parameters(['character-lockouts' => 'lockout'])
            ->names('character-lockouts')
            ->except(['create', 'show', 'edit']);
    });

    Route::delete('/dynamic-zone-members/{dynamicZoneMember}', [DynamicZoneMemberController::class, 'destroy'])
        ->name('dynamic-zone-members.destroy');

    // factions
    Route::prefix('factions')->name('factions.')->group(function () {
        Route::get('/edit', [FactionController::class, 'edit'])->name('edit');
        Route::post('/', [FactionController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{faction}', [FactionController::class, 'update'])->name('update');
        Route::delete('{faction}', [FactionController::class, 'destroy'])->name('destroy');
        Route::get('/search', [FactionController::class, 'search'])->name('search');
        Route::get('/options', [FactionController::class, 'options'])->name('options');
        Route::get('{faction}/npcs', [FactionController::class, 'npcsOnFaction'])->name('npcs.primary');
        Route::get('{faction}/npcs/{effect}', [FactionController::class, 'npcsByFactionEffect'])->name('npcs.effect');

        Route::get('{faction}/mods', [FactionListModController::class, 'index'])->name('mods.index');
        Route::post('{faction}/mods', [FactionListModController::class, 'store'])->name('mods.store');
        Route::put('{faction}/mods/{mod}', [FactionListModController::class, 'update'])->name('mods.update');
        Route::delete('{faction}/mods/{mod}', [FactionListModController::class, 'destroy'])->name('mods.destroy');

        // association resource
        Route::resource('associations', FactionAssociationController::class)
            ->parameters(['associations' => 'association'])
            ->names('associations')
            ->except(['create', 'show', 'edit']);

        // characters
        Route::get('characters', [FactionValueController::class, 'index'])->name('characters.index');
        Route::post('characters/{char_id?}', [FactionValueController::class, 'store'])->name('characters.store');
        Route::match(['put', 'patch'], 'characters/{char_id}/{faction_id}', [FactionValueController::class, 'update'])
            ->name('characters.update');
        Route::delete('characters/{char_id}/{faction_id}', [FactionValueController::class, 'destroy'])->name('characters.destroy');
    });

    // free id's lookup
    Route::get('/free-ids', [IdPickerController::class, 'freeIds'])->name('free_ids');

    // guilds
    Route::prefix('guilds')->name('guilds.')->group(function () {
        Route::get('/', [GuildController::class, 'index'])->name('index');
        Route::get('/show/{guild}', [GuildController::class, 'show'])->name('show');
        Route::get('/search', [GuildController::class, 'search'])->name('search');
        Route::post('{guild}/members', [GuildController::class, 'storeMember'])->name('members.store');
        Route::delete('{guild}/members/{member}', [GuildController::class, 'destroyMember'])->name('members.destroy');
    });

    // global loot
    Route::resource('global-loot', GlobalLootController::class);

    // items
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])
            ->middleware(NoCache::class)
            ->name('index');
        Route::match(['post', 'put'], '/preview', [ItemController::class, 'preview'])->name('preview');
        Route::get('{item}/edit', [ItemController::class, 'edit'])->name('edit');
        Route::post('{item}/clone', [ItemController::class, 'clone'])->name('clone');
        Route::post('/', [ItemController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{item}', [ItemController::class, 'update'])->name('update');
        Route::delete('{item}', [ItemController::class, 'destroy'])->name('destroy');
        Route::get('/popup/{item}', [ItemController::class, 'popup'])->name('popup');
        Route::get('/search', [ItemController::class, 'search'])->name('search');

        // evolving items
        Route::get('evolving-items', [ItemEvolvingDetailController::class, 'index'])->name('evolving-items.index');
        Route::post('evolving-items', [ItemEvolvingDetailController::class, 'store'])->name('evolving-items.store');
        Route::get('evolving-items/options', [ItemEvolvingDetailController::class, 'options'])
            ->name('evolving-items.options');
        Route::match(['put', 'patch'], 'evolving-items/{evolving_item}', [ItemEvolvingDetailController::class, 'update'])
            ->name('evolving-items.update');
        Route::delete('evolving-items/{evolving_item}', [ItemEvolvingDetailController::class, 'destroy'])
            ->name('evolving-items.destroy');
    });

    Route::prefix('ldon-trap-templates')->name('ldon-trap-templates.')->group(function () {
        Route::get('/', [LdonTrapTemplateController::class, 'index'])->name('index');
        Route::post('/', [LdonTrapTemplateController::class, 'store'])->name('store');
        Route::get('{trapTemplate}/edit', [LdonTrapTemplateController::class, 'edit'])->name('edit');
        Route::put('{trapTemplate}', [LdonTrapTemplateController::class, 'update'])->name('update');
        Route::delete('{trapTemplate}', [LdonTrapTemplateController::class, 'destroy'])->name('destroy');

        Route::post('{trapTemplate}/entries', [LdonTrapEntryController::class, 'store'])
            ->name('entries.store');
        Route::put('{trapTemplate}/entries/{trapEntry}', [LdonTrapEntryController::class, 'update'])
            ->name('entries.update');
        Route::delete('{trapTemplate}/entries/{trapEntry}', [LdonTrapEntryController::class, 'destroy'])
            ->name('entries.destroy');
    });

    // loot
    Route::prefix('loot')->name('loot.')->group(function () {
        Route::get('/', [LootTableController::class, 'index'])
            ->middleware(NoCache::class)
            ->name('index');
        Route::get('{loottable}/edit', [LootTableController::class, 'edit'])->name('edit');
        Route::post('/', [LootTableController::class, 'store'])->name('store');
        Route::put('{loottable}', [LootTableController::class, 'update'])->name('update');
        Route::match(['put', 'patch'], '{loottable}/lootdrop', [LootTableController::class, 'updateLootdrop'])
            ->name('update-lootdrop');
        Route::delete('{loottable}', [LootTableController::class, 'destroy'])->name('destroy');
        Route::post('{loottable}/unlink', [LootTableController::class, 'unlink'])->name('unlink');
        Route::post('{loottable}/clone', [LootTableController::class, 'clone'])->name('clone');
        Route::get('/search', [LootTableController::class, 'search'])->name('search');

        Route::post('{loottable}/entries', [LootTableEntryController::class, 'store'])->name('entries.store');
        Route::put('{loottable}/entries/{lootdrop}', [LootTableEntryController::class, 'update'])->name('entries.update');
        Route::delete('{loottable}/entries/{lootdrop}', [LootTableEntryController::class, 'destroy'])
            ->name('entries.destroy');

        // loot drops
        Route::post('drops/{loottable}/link', [LootDropController::class, 'link'])->name('drops.link');
        Route::get('drops/search', [LootDropController::class, 'search'])->name('drops.search');
        Route::get('drops', [LootDropController::class, 'index'])->name('drops.index');
        Route::get('drops/{drop}/tables', [LootDropController::class, 'tables'])->name('drops.tables');
        Route::post('{loottable}/drops', [LootDropController::class, 'store'])->name('drops.store');
        Route::get('drops/{drop}/edit', [LootDropController::class, 'edit'])->name('drops.edit');
        Route::put('drops/{drop}', [LootDropController::class, 'update'])->name('drops.update');
        Route::post('drops/{drop}/clone', [LootDropController::class, 'clone'])->name('drops.clone');
        Route::post('drops/{drop}/unlink', [LootDropController::class, 'unlink'])->name('drops.unlink');
        Route::delete('drops/{drop}', [LootDropController::class, 'destroy'])->name('drops.destroy');

        // loot drop items
        Route::post('drops/{drop}/entries', [LootDropEntryController::class, 'store'])->name('drops.entries.store');
        Route::put('drops/{drop}/entries/{item}', [LootDropEntryController::class, 'update'])
            ->name('drops.entries.update');
        Route::delete('drops/{drop}/entries/{item}', [LootDropEntryController::class, 'destroy'])->name('drops.entries.destroy');
    });

    // mail
    Route::prefix('mail')->name('mail.')->group(function () {
        Route::get('/', [MailController::class, 'index'])->name('index');
        Route::post('/', [MailController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{mail}', [MailController::class, 'update'])->name('update');
        Route::delete('/', [MailController::class, 'destroy'])->name('destroy');
    });

    // merchants
    Route::prefix('merchants')->name('merchants.')->group(function () {
        Route::get('/', [MerchantController::class, 'index'])->name('index');
        Route::post('{npc}/items', [MerchantController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{merchant}', [MerchantController::class, 'update'])
            ->where('merchant', '[0-9]+')
            ->name('update');

        Route::delete('{merchant}/{slot}', [MerchantController::class, 'destroy'])
            ->where(['merchant' => '[0-9]+', 'slot' => '[0-9]+'])
            ->name('destroy');

        // temp items
        Route::prefix('temp')->name('temp.')->group(function () {
            Route::get('{merchant}', [MerchantTempController::class, 'index'])->name('index');
            Route::post('/', [MerchantTempController::class, 'store'])->name('store');
            Route::match(['put', 'patch'], '{merchant}', [MerchantTempController::class, 'update'])->name('update');
            Route::delete('{merchant}/{slot}/{zone_id}/{instance_id}', [MerchantTempController::class, 'destroy'])
                ->where([
                    'merchant' => '[0-9]+',
                    'slot' => '[0-9]+',
                    'zone_id' => '[0-9]+',
                    'instance_id' => '[0-9]+',
                ])
                ->name('destroy');

            Route::delete('{merchant}/clear-all', [MerchantTempController::class, 'clearAll'])->name('clear-all');
        });
    });

    // mounts
    Route::prefix('mounts')->name('mounts.')->group(function () {
        Route::get('/', [MountController::class, 'index'])->name('index');
        Route::post('/', [MountController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{horse}', [MountController::class, 'update'])->name('update');
        Route::delete('{horse}', [MountController::class, 'destroy'])->name('destroy');
    });

    Route::resource('npc-emotes', NpcEmoteController::class);

    // npc spells
    Route::get('/npc-spells/search', [NpcSpellController::class, 'search'])->name('search');
    Route::get('/npc-spells/options', [NpcSpellController::class, 'options'])->name('options');
    Route::resource('npc-spells', NpcSpellController::class);
    Route::resource('npc-spell-entry', NpcSpellEntryController::class);
    Route::resource('npc-spell-effects', NpcSpellEffectController::class);
    Route::resource('npc-spell-effect-entry', NpcSpellEffectEntryController::class);

    // npcs
    Route::prefix('npcs')->name('npcs.')->group(function () {
        Route::get('/', [NpcTypeController::class, 'index'])
            ->middleware(NoCache::class)
            ->name('index');
        Route::get('{npc}/edit', [NpcTypeController::class, 'edit'])->name('edit');
        Route::match(['post', 'put'], '/preview', [NpcTypeController::class, 'preview'])->name('preview');
        Route::post('{npc}/clone', [NpcTypeController::class, 'clone'])->name('clone');
        Route::match(['put', 'patch'], '{npc}', [NpcTypeController::class, 'update'])->name('update');
        Route::match(['put', 'patch'], '{npc}/faction', [NpcTypeController::class, 'updateFaction'])
            ->name('update-faction');
        Route::match(['put', 'patch'], '{npc}/loottable', [NpcTypeController::class, 'updateLoottable'])
            ->name('update-loottable');
        Route::match(['put', 'patch'], '{npc}/spellset', [NpcTypeController::class, 'updateSpellset'])
            ->name('update-spellset');
        Route::delete('{npc}', [NpcTypeController::class, 'destroy'])->name('destroy');
        Route::get('/search', [NpcTypeController::class, 'search'])->name('search');
        Route::get('/races', [NpcTypeController::class, 'races'])->name('races');
        Route::get('/races/{id}', [NpcTypeController::class, 'race'])->name('races.show');
        Route::get('{npc}/zones', [NpcTypeController::class, 'zones'])->name('zones');

        Route::prefix('factions')->name('factions.')->group(function () {
            Route::get('/search', [NpcFactionController::class, 'search'])->name('search');
        });

        Route::resource('primary-factions', NpcFactionController::class)
            ->only(['store', 'update', 'destroy'])
            ->names('primary-factions');

        Route::resource('faction-entries', NpcFactionEntryController::class)
            ->only(['store', 'update', 'destroy'])
            ->names('faction-entries');
    });

    // parcels
    Route::resource('parcels', ParcelController::class);

    // pets
    Route::prefix('pets')->name('pets.')->group(function () {
        // Standard Pet Routes
        Route::get('/', [PetController::class, 'index'])->name('index');
        Route::post('/', [PetController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{pet}', [PetController::class, 'update'])->name('update');
        Route::delete('/', [PetController::class, 'destroy'])->name('destroy');
        Route::get('/popup/{pet}', [PetController::class, 'popup'])->name('popup');

        Route::resource('equipment', PetEquipmentController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::post('equipment-items', [PetEquipmentEntryController::class, 'store'])->name('equipment-items.store');
        Route::match(['put', 'patch'], 'equipment-items/{set}/{slot}', [PetEquipmentEntryController::class, 'update'])->name('equipment-items.update');
        Route::delete('equipment-items/{set}/{slot}', [PetEquipmentEntryController::class, 'destroy'])->name('equipment-items.destroy');
    });

    // player logs
    Route::prefix('player-logs')->name('player-logs.')->group(function () {
        Route::get('/', [PlayerLogController::class, 'index'])->name('index');
        Route::delete('/', [PlayerLogController::class, 'destroy'])->name('destroy');

        Route::get('settings', [PlayerLogSettingController::class, 'index'])->name('settings.index');
        Route::patch('settings/{setting}', [PlayerLogSettingController::class, 'update'])->name('settings.update');
    });

    // qglobals
    Route::prefix('qglobals')->name('qglobals.')->group(function () {
        Route::get('/', [QuestGlobalController::class, 'index'])->name('index');
        Route::post('/', [QuestGlobalController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '/', [QuestGlobalController::class, 'update'])->name('update');
        Route::delete('/', [QuestGlobalController::class, 'destroy'])->name('destroy');
    });

    // server rules
    Route::prefix('server-rules')->name('server-rules.')->group(function () {
        Route::get('/', [ServerRuleController::class, 'index'])->name('index');
        Route::patch('/{rule_name}', [ServerRuleController::class, 'update'])
            ->where('rule_name', '.*')
            ->name('update');
    });

    // spawn groups, spawnpoints, and entries
    Route::prefix('spawngroups')->name('spawngroups.')->group(function () {
        Route::get('/', [SpawnGroupController::class, 'index'])->name('index');
        Route::get('{spawngroup}/edit', [SpawnGroupController::class, 'edit'])->name('edit');
        Route::post('/', [SpawnGroupController::class, 'store'])->name('store');
        Route::match(['put', 'patch'], '{spawngroup}', [SpawnGroupController::class, 'update'])->name('update');
        Route::delete('{spawngroup}', [SpawnGroupController::class, 'destroy'])->name('destroy');

        Route::prefix('{spawngroup}/spawnpoints')->name('spawnpoints.')->group(function () {
            Route::post('/', [SpawnPointController::class, 'store'])->name('store');
            Route::match(['put', 'patch'], '{spawnpoint}', [SpawnPointController::class, 'update'])->name('update');
            Route::delete('{spawnpoint}', [SpawnPointController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{spawngroup}/entries')->name('entries.')->group(function () {
            Route::post('/', [SpawnEntryController::class, 'store'])->name('store');
            Route::match(['put', 'patch'], '{npcID}', [SpawnEntryController::class, 'update'])->name('update');
            Route::delete('{npcID}', [SpawnEntryController::class, 'destroy'])->name('destroy');
        });
    });

    // spells
    Route::prefix('spells')->name('spells.')->group(function () {
        Route::get('/', [SpellController::class, 'index'])
            ->middleware(NoCache::class)
            ->name('index');
        Route::get('/tz', [SpellController::class, 'tz'])->name('tz');
        Route::get('/search', [SpellController::class, 'search'])->name('search');
        Route::get('/spelleffects', [SpellController::class, 'spelleffects'])->name('spelleffects');
        Route::get('/spelleffects/{id}', [SpellController::class, 'spelleffect'])->name('spelleffects.show');
        Route::get('/animations/list', [SpellController::class, 'animationsList'])->name('animations.list');
        Route::get('/effects', [SpellController::class, 'effects'])->name('effects');
        Route::match(['post', 'put'], '/preview', [SpellController::class, 'preview'])->name('preview');
        Route::get('{spell}/edit', [SpellController::class, 'edit'])->name('edit');
        Route::post('{spell}/clone', [SpellController::class, 'clone'])->name('clone');
        Route::match(['put', 'patch'], '{spell}', [SpellController::class, 'update'])->name('update');
        Route::delete('{spell}', [SpellController::class, 'destroy'])->name('destroy');
        Route::get('/popup/{spell}', [SpellController::class, 'popup'])->name('popup');
        Route::get('/defaults/{spa}', [SpellController::class, 'defaults'])->name('defaults');
    });

    // starting items
    Route::resource('starting-items', StartingItemController::class);

    // tasks
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');

        // shared tasks
        Route::prefix('shared-tasks')->name('shared-tasks.')->group(function () {
            Route::get('/active', [SharedTaskController::class, 'active'])->name('active');
            Route::get('/completed', [SharedTaskController::class, 'completed'])->name('completed');
        });

        Route::get('/active', [TaskController::class, 'active'])->name('active');
        Route::delete('/active/{taskid}/{charid}', [TaskController::class, 'deleteActive'])->name('delete-active');
        Route::get('/completed', [TaskController::class, 'completed'])->name('completed');
        Route::delete('/completed/{taskid}/{charid}', [TaskController::class, 'deleteCompleted'])->name('delete-completed');

        Route::get('timer-groups', [TaskController::class, 'timerGroups'])->name('timer-groups');
        Route::get('timer-groups-detail', [TaskController::class, 'timerGroupsDetail'])->name('timer-groups-detail');

        Route::get('{task}/edit', [TaskController::class, 'edit'])->name('edit')->where('task', '[0-9]+');
        Route::post('{task}/clone', [TaskController::class, 'clone'])->name('clone')->where('task', '[0-9]+');
        Route::put('{task}', [TaskController::class, 'update'])->name('update')->where('task', '[0-9]+');
        Route::delete('{task}', [TaskController::class, 'destroy'])->name('destroy');

        Route::post('{task}/activities', [TaskActivityController::class, 'store'])->name('activities.store');
        Route::put('{task}/activities/{activity}', [TaskActivityController::class, 'update'])->name('activities.update');
        Route::delete('{task}/activities/{activity}', [TaskActivityController::class, 'destroy'])
            ->name('activities.destroy');
        Route::post('{task}/activities/reorder', [TaskActivityController::class, 'reorder'])->name('activities.reorder');
    });

    // titles
    Route::resource('titles', TitleController::class);

    // trader-audit
    Route::prefix('trader-audit')->name('trader-audit.')->group(function () {
        Route::get('/', [TraderAuditController::class, 'index'])->name('index');
        Route::delete('/', [TraderAuditController::class, 'destroy'])->name('destroy');
    });

    // tradeskills
    Route::prefix('tradeskills')->name('tradeskills.')->group(function () {
        Route::resource('container-templates', TradeskillContainerTemplateController::class)->parameters([
            'container-templates' => 'tradeskillContainerTemplate',
        ]);

        Route::get(
            'container-templates/{tradeskillContainerTemplate}/items',
            [TradeskillContainerTemplateController::class, 'items']
        )->name('container-templates.items');

        Route::get('/', [TradeskillRecipeController::class, 'index'])->name('index');
        Route::post('/', [TradeskillRecipeController::class, 'store'])->name('store');
        Route::post('{recipe}/clone', [TradeskillRecipeController::class, 'clone'])->name('clone');
        Route::get('{recipe}/edit', [TradeskillRecipeController::class, 'edit'])->name('edit');
        Route::put('{recipe}', [TradeskillRecipeController::class, 'update'])->name('update');
        Route::delete('{recipe}', [TradeskillRecipeController::class, 'destroy'])->name('destroy');

        Route::post('{recipe}/entries', [TradeskillRecipeEntryController::class, 'store'])->name('entries.store');
        Route::put('entries/{entry}', [TradeskillRecipeEntryController::class, 'update'])->name('entries.update');
        Route::delete('entries/{entry}', [TradeskillRecipeEntryController::class, 'destroy'])->name('entries.destroy');
    });

    // tribute
    Route::prefix('tribute')->name('tribute.')->group(function () {
        Route::get('/', [TributeController::class, 'index'])->name('index');
        Route::post('/', [TributeController::class, 'store'])->name('store');
        Route::put('/{id}/{isguild}', [TributeController::class, 'update'])->name('update');
        Route::delete('/{id}/{isguild}', [TributeController::class, 'destroy'])->name('destroy');

        Route::prefix('{tribute_id}/levels')->name('levels.')->group(function () {
            Route::post('/', [TributeLevelController::class, 'store'])->name('store');
            Route::put('{level}', [TributeLevelController::class, 'update'])->name('update');
            Route::delete('{level}', [TributeLevelController::class, 'destroy'])->name('destroy');
        });
    });

    // variables
    Route::prefix('variables')->name('variables.')->group(function () {
        Route::get('/', [VariableController::class, 'index'])->name('index');
        Route::match(['put', 'patch'], '{variable}', [VariableController::class, 'update'])->name('update');
    });

    // zones
    Route::prefix('zones')->name('zones.')->group(function () {
        Route::get('/', [ZoneController::class, 'index'])->name('index');
        Route::get('{zone}/edit', [ZoneController::class, 'edit'])->name('edit');
        Route::post('{zone}/clone', [ZoneController::class, 'clone'])->name('clone');
        Route::match(['put', 'patch'], '{zone}', [ZoneController::class, 'update'])->name('update');
        Route::get('/options', [ZoneController::class, 'options'])->name('options');
        Route::get('/search', [ZoneController::class, 'search'])->name('search');

        Route::prefix('graveyards')->name('graveyards.')->group(function () {
            Route::get('/', [GraveyardController::class, 'index'])->name('index');
            Route::post('/', [GraveyardController::class, 'store'])->name('store');
            Route::put('{graveyard}', [GraveyardController::class, 'update'])->name('update');
            Route::delete('{graveyard}', [GraveyardController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:short_name}/zone-points')->name('zone-points.')->group(function () {
            Route::post('/', [ZonePointController::class, 'store'])->name('store');
            Route::put('{zonePoint}', [ZonePointController::class, 'update'])->name('update');
            Route::delete('{zonePoint}', [ZonePointController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:zoneidnumber}/grids')->name('grids.')->group(function () {
            Route::post('/', [GridController::class, 'store'])->name('store');
            Route::patch('{gridid}', [GridController::class, 'update'])->name('update');
            Route::delete('{gridid}', [GridController::class, 'destroy'])->name('destroy');

            Route::prefix('{gridid}/entries')->name('entries.')->group(function () {
                Route::post('/', [GridEntryController::class, 'store'])->name('store');
                Route::patch('{number}', [GridEntryController::class, 'update'])->name('update');
                Route::delete('{number}', [GridEntryController::class, 'destroy'])->name('destroy');
            });
        });

        Route::prefix('{zone:zoneidnumber}/blocked-spells')->name('blocked-spells.')->group(function () {
            Route::post('/', [BlockedSpellController::class, 'store'])->name('store');
            Route::put('{blockedSpell}', [BlockedSpellController::class, 'update'])->name('update');
            Route::delete('{blockedSpell}', [BlockedSpellController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:short_name}/doors')->name('doors.')->group(function () {
            Route::post('/', [DoorController::class, 'store'])->name('store');
            Route::put('{door}', [DoorController::class, 'update'])->name('update');
            Route::delete('{door}', [DoorController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:zoneidnumber}/ground-spawns')->name('ground-spawns.')->group(function () {
            Route::post('/', [GroundSpawnController::class, 'store'])->name('store');
            Route::put('{groundspawn}', [GroundSpawnController::class, 'update'])->name('update');
            Route::delete('{groundspawn}', [GroundSpawnController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:zoneidnumber}/fishing')->name('fishing.')->group(function () {
            Route::post('/', [FishingController::class, 'store'])->name('store');
            Route::put('{fish}', [FishingController::class, 'update'])->name('update');
            Route::delete('{fish}', [FishingController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:zoneidnumber}/forage')->name('forage.')->group(function () {
            Route::post('/', [ForageController::class, 'store'])->name('store');
            Route::put('{forage}', [ForageController::class, 'update'])->name('update');
            Route::delete('{forage}', [ForageController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:short_name}/traps')->name('traps.')->group(function () {
            Route::post('/', [TrapController::class, 'store'])->name('store');
            Route::put('{trap}', [TrapController::class, 'update'])->name('update');
            Route::delete('{trap}', [TrapController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{zone:zoneidnumber}/objects')->name('objects.')->group(function () {
            Route::post('/', [ObjectController::class, 'store'])->name('store');
            Route::put('{obj}', [ObjectController::class, 'update'])->name('update');
            Route::delete('{obj}', [ObjectController::class, 'destroy'])->name('destroy');
        });
    });

    // update checker (simple JSON endpoint used by the frontend)
    Route::get('/update/check', [UpdateController::class, 'check'])->name('update.check');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
