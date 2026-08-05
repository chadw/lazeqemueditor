@php
    $metadata = array_replace([
        'component_types' => [0 => 'Type 0', 1 => 'Type 1', 2 => 'Type 2', 3 => 'Presentation only'],
        'event_types' => [
            0 => 'Manual', 1 => 'Level', 2 => 'NPC type kill', 3 => 'NPC race kill',
            4 => 'Task complete', 5 => 'Zone enter', 6 => 'Loot item', 7 => 'Own item',
            8 => 'Tradeskill success', 9 => 'Skill value', 10 => 'Alternate advancement',
            11 => 'Achievement complete', 12 => 'NPC name kill', 13 => 'Skill cap',
        ],
        'progress_modes' => [0 => 'Increment', 1 => 'Highest', 2 => 'Set', 3 => 'Boolean'],
        'behaviors' => [0 => 'Required', 1 => 'Optional', 2 => 'Unlock', 3 => 'Visibility', 4 => 'Display only', 5 => 'Blocker'],
        'reward_types' => [0 => 'Item', 1 => 'Experience', 2 => 'AA points', 3 => 'Copper', 4 => 'Alternate currency', 5 => 'Title set'],
    ], $metadata ?? []);

    $initialEditor = $editor;
    $oldInput = session()->getOldInput();
    if ($oldInput) {
        foreach ([
            'id', 'name', 'description', 'icon_id', 'points', 'reward_display', 'world_display_flag',
            'definition_version', 'reset_on_version_change', 'enabled', 'associations', 'components',
            'rewards', 'restrictions', 'suggested_reward_set_id',
            'suggested_component_id',
        ] as $key) {
            if (array_key_exists($key, $oldInput)) {
                $initialEditor[$key] = $oldInput[$key];
            }
        }

        $initialEditor['reward_set'] = (int) ($oldInput['has_reward_set'] ?? 0) === 1
            ? ($oldInput['reward_set'] ?? [
                'reward_set_id' => $editor['suggested_reward_set_id'] ?? $editor['id'] ?? 1,
                'title' => '',
                'enabled' => 1,
                'options' => [],
            ])
            : null;
    }

    // Achievement criteria store the zero-based SkillUseTypes ID. The grouped
    // everquest.skills config is presentation-oriented and cannot be flattened
    // safely because PHP/Laravel reindexes its numeric keys.
    $skillOptions = \App\Support\Achievements\AchievementMetadata::SKILL_USE_TYPES;
    $raceOptions = [0 => 'Any NPC race'] + config('everquest.db_races', []);
    $classOptions = [0 => 'Any class'] + config('everquest.classes', []);
@endphp

<div x-data="achievementEditor({
        editor: {{ Js::from($initialEditor) }},
        metadata: {{ Js::from($metadata) }},
        suggestedRewardSetId: {{ (int) ($editor['suggested_reward_set_id'] ?? $editor['id'] ?? 1) }}
    })"
    x-init="init()"
    class="space-y-4">

    <x-top-links>
        <x-slot name="left">
            <a href="{{ route('achievements.index') }}" class="btn btn-soft">
                <x-ui.icon name="square-arrow-left" /> Achievements
            </a>
            <a href="{{ route('achievement-categories.index') }}" class="btn btn-soft btn-accent">Categories</a>
        </x-slot>
        @unless($isCreate)
            <form method="POST" action="{{ route('achievements.clone', $editor['id']) }}"
                onsubmit="return confirm('Clone as a new disabled definition? Cast restrictions are intentionally omitted.')">
                @csrf
                <button class="btn btn-soft btn-accent"><x-ui.icon name="clone" /> Safe Clone</button>
            </form>
        @endunless
    </x-top-links>

    <x-ui.alert-warning>
        IDs are durable runtime identities. For incompatible deployed changes, increment the definition version and
        choose the reset policy deliberately. After saving, run <code>#reload achievements global</code> to validate and publish.
    </x-ui.alert-warning>

    @if($errors->any())
        <div role="alert" class="alert alert-error items-start">
            <x-ui.icon name="warning" />
            <div>
                <div class="font-semibold">The definition was not saved.</div>
                <ul class="list-disc ml-5 mt-1 text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="space-y-4">
        @csrf
        @if($formMethod !== 'POST') @method($formMethod) @endif

        <div role="tablist" class="tabs tabs-box bg-base-100 shadow overflow-x-auto flex-nowrap justify-start sticky top-0 z-20">
            <button type="button" role="tab" class="tab whitespace-nowrap" title="Name, presentation, score, enable state, and durable definition version"
                :class="{ 'tab-active': tab === 'general' }" @click="tab = 'general'">General</button>
            <button type="button" role="tab" class="tab whitespace-nowrap" title="Choose where this definition appears in the client category tree"
                :class="{ 'tab-active': tab === 'categories' }" @click="tab = 'categories'">
                Categories <span class="badge badge-xs ml-1" x-text="editor.associations.length"></span>
            </button>
            <button type="button" role="tab" class="tab whitespace-nowrap" title="Author client rows and the server events that advance them"
                :class="{ 'tab-active': tab === 'components' }" @click="tab = 'components'">
                Components <span class="badge badge-xs ml-1" x-text="editor.components.length"></span>
            </button>
            <button type="button" role="tab" class="tab whitespace-nowrap" title="Configure automatic grants and selectable reward graphs"
                :class="{ 'tab-active': tab === 'rewards' }" @click="tab = 'rewards'">
                Rewards <span class="badge badge-xs ml-1" x-text="editor.rewards.length"></span>
            </button>
            <button type="button" role="tab" class="tab whitespace-nowrap" title="Connect achievement completion to spell cast restrictions"
                :class="{ 'tab-active': tab === 'restrictions' }" @click="tab = 'restrictions'">
                Cast Restrictions <span class="badge badge-xs ml-1" x-text="editor.restrictions.length"></span>
            </button>
            <button type="button" role="tab" class="tab whitespace-nowrap" title="Review live authoring checks before saving and reloading the server catalog"
                :class="{ 'tab-active': tab === 'validation' }" @click="tab = 'validation'">
                Validation
                <span class="badge badge-xs badge-warning ml-1" x-show="validationIssues().length" x-text="validationIssues().length"></span>
            </button>
            <button type="button" role="tab" class="tab whitespace-nowrap" title="Open the built-in event, behavior, reward, and publishing reference"
                :class="{ 'tab-active': tab === 'guide' }" @click="tab = 'guide'">Authoring Guide</button>
        </div>

        <section x-show="tab === 'general'" x-cloak>@include('achievements.partials.general')</section>
        <section x-show="tab === 'categories'" x-cloak>@include('achievements.partials.associations')</section>
        <section x-show="tab === 'components'" x-cloak>@include('achievements.partials.components')</section>
        <section x-show="tab === 'rewards'" x-cloak>@include('achievements.partials.rewards')</section>
        <section x-show="tab === 'restrictions'" x-cloak>@include('achievements.partials.restrictions')</section>
        <section x-show="tab === 'validation'" x-cloak>@include('achievements.partials.validation')</section>
        <section x-show="tab === 'guide'" x-cloak>@include('achievements.partials.guide')</section>

        <div class="sticky bottom-0 z-20 card bg-neutral/95 backdrop-blur border border-base-content/10 shadow-xl">
            <div class="card-body py-3 flex-row items-center justify-between gap-4">
                <div class="text-sm opacity-70">
                    <span x-text="editor.enabled ? 'Enabled definition' : 'Disabled draft'"></span>
                    · Version <span x-text="editor.definition_version"></span>
                    · <span x-text="editor.components.length"></span> components
                </div>
                <button type="submit" class="btn btn-soft btn-success">
                    <x-ui.icon name="save" /> {{ $isCreate ? 'Create Achievement' : 'Save Definition Graph' }}
                </button>
            </div>
        </div>
    </form>
</div>
