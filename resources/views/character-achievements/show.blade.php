@extends('layouts.app')
@section('title', "Achievements: {$character->name}")
@section('page-title', "Character Achievements: {$character->name}")

@section('content')
    @php
        $isOnline = (int) ($character->ingame ?? 0) !== 0;
        $stateBadgeClasses = [
            'completed' => 'badge-success',
            'in_progress' => 'badge-warning',
            'not_started' => 'badge-ghost',
        ];
    @endphp

    <x-top-links>
        <x-slot name="left">
            <a href="{{ route('character-achievements.index') }}" class="btn btn-soft tooltip tooltip-bottom">
                ← Characters
            </a>
            <a href="{{ route('characters.show', $character->id) }}" class="btn btn-soft btn-accent">Character Sheet</a>
            <span class="badge badge-soft badge-default">ID {{ $character->id }}</span>
            <span class="badge badge-soft badge-default">
                Level {{ $character->level }} {{ eq_class($character->class) }}
            </span>
            @if ($isOnline)
                <span class="badge badge-error badge-soft tooltip tooltip-bottom"
                    data-tip="character_data.ingame is nonzero; offline force-completion is blocked">
                    In game
                </span>
            @else
                <span class="badge badge-success badge-soft tooltip tooltip-bottom"
                    data-tip="character_data.ingame is zero; offline-only completion is available">
                    Offline
                </span>
            @endif
        </x-slot>
        <a href="{{ route('achievements.index') }}" class="btn btn-soft btn-info float-end">Achievement Content</a>
    </x-top-links>

    <div class="space-y-5">
        <x-ui.alert-warning>
            <div class="font-semibold">These controls edit server-owned durable state directly.</div>
            <p class="mt-1 text-sm">
                {{ $metadata['force_completion_warning'] }} Exact progress and resets also do not refresh an
                already loaded client's in-memory state, so logging the character out before administrative edits
                is strongly recommended. Every administrative write is serialized with the EQEmu character-achievement lock.
            </p>
            <p class="mt-1 text-sm font-medium">
                Reward ledgers are at-most-once safety boundaries. They are preserved by ordinary reset and are
                changed only by the explicitly labeled high-risk controls below.
            </p>
        </x-ui.alert-warning>
        <x-ui.card>
            <x-slot:header>
                <div class="font-semibold">Catalog filters</div>
            </x-slot:header>
            @include('character-achievements.partials.filters')
        </x-ui.card>
        <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-base-content/70">
            <span>
                Showing {{ $achievements->firstItem() ?? 0 }}-{{ $achievements->lastItem() ?? 0 }} of
                {{ number_format($achievements->total()) }} matching definitions.
            </span>
            <span>
                Page {{ $achievements->currentPage() }} / {{ max($achievements->lastPage(), 1) }}
            </span>
        </div>

        <div class="space-y-4">
            @forelse ($achievements as $achievement)
                @php
                    $completion = $achievement->completion;
                    $stateClass = $stateBadgeClasses[$achievement->durable_state] ?? 'badge-ghost';
                    $hasCoreState = $completion
                        || $achievement->progress->isNotEmpty()
                        || $achievement->pending_updates->isNotEmpty();
                    $hasRewardState = $achievement->reward_ledgers->isNotEmpty()
                        || $achievement->reward_selections->isNotEmpty();
                    $hasAnyState = $hasCoreState || $hasRewardState;
                    $definedRewardIds = $achievement->rewards
                        ->pluck('reward_id')
                        ->map(fn ($id) => (string) $id)
                        ->all();
                    $orphanRewardLedgers = $achievement->reward_ledgers
                        ->reject(fn ($ledger) => in_array((string) $ledger->reward_id, $definedRewardIds, true))
                        ->values();
                    $definedSetIds = $achievement->reward_sets
                        ->pluck('reward_set_id')
                        ->map(fn ($id) => (string) $id)
                        ->all();
                    $orphanSelections = $achievement->reward_selections
                        ->reject(fn ($selection) => in_array((string) $selection->reward_set_id, $definedSetIds, true))
                        ->values();
                @endphp

                <details class="collapse collapse-arrow border border-base-300 bg-base-100 shadow-sm"
                    @if ($achievement->has_version_mismatch || $achievement->reward_needs_attention || $achievement->pending_updates->isNotEmpty()) open @endif>
                    <summary class="collapse-title pr-12">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-lg font-semibold">{{ $achievement->name ?: '(Unnamed achievement)' }}</span>
                                    <span class="badge badge-sm badge-outline tooltip"
                                        data-tip="Global achievement definition ID">{{ $achievement->id }}</span>
                                    <span class="badge badge-sm {{ $achievement->enabled ? 'badge-info' : 'badge-neutral' }} badge-soft tooltip"
                                        data-tip="Only enabled definitions are loaded by AchievementManager">
                                        {{ $achievement->enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                    <span class="badge badge-sm {{ $stateClass }} badge-soft tooltip"
                                        data-tip="Derived from this character's durable completion and positive progress rows">
                                        {{ str_replace('_', ' ', ucfirst($achievement->durable_state)) }}
                                    </span>
                                    @if ($achievement->has_version_mismatch)
                                        <span class="badge badge-sm badge-error tooltip"
                                            data-tip="At least one durable row was written under a different definition version">
                                            Version mismatch
                                        </span>
                                    @endif
                                    @if ($achievement->reward_needs_attention)
                                        <span class="badge badge-sm badge-error badge-outline tooltip"
                                            data-tip="A reward or selection ledger is retryable, ambiguous, or in flight">
                                            Reward attention
                                        </span>
                                    @endif
                                    @if ($achievement->pending_updates->isNotEmpty())
                                        <span class="badge badge-sm badge-warning badge-outline tooltip"
                                            data-tip="Durable cross-zone state-update requests exist for this character and achievement">
                                            {{ $achievement->pending_updates->count() }} queued
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 line-clamp-2 text-sm text-base-content/70">
                                    {{ $achievement->description ?: 'No presentation description.' }}
                                </p>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @forelse ($achievement->categories as $category)
                                        <span class="badge badge-xs badge-ghost tooltip"
                                            data-tip="Direct category association {{ $category->category_id }}">
                                            {{ $category->name }}
                                        </span>
                                    @empty
                                        <span class="badge badge-xs badge-error badge-outline tooltip"
                                            data-tip="Enabled runtime definitions require a valid category association">
                                            No category
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-1 text-xs text-base-content/65">
                                <span class="tooltip" data-tip="Current global achievements.version">
                                    Version {{ $achievement->version }}
                                </span>
                                <span class="tooltip" data-tip="Client-visible achievement point value">
                                    {{ number_format((int) $achievement->points) }} points
                                </span>
                                <span>{{ $achievement->components->count() }} components</span>
                            </div>
                        </div>
                    </summary>

                    <div class="collapse-content space-y-5">
                        @if ($achievement->has_version_mismatch)
                            <div role="alert" class="alert alert-soft alert-error items-start">
                                <div>
                                    <div class="font-semibold">Durable version mismatch</div>
                                    <p class="text-sm">
                                        Definition version is {{ $achievement->version }}. With
                                        <code>reset_on_version_change={{ (int) $achievement->reset_on_version_change }}</code>,
                                        the runtime may reset stale state during load or retain it according to content policy.
                                        Inspect each row before changing it.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
                            <div class="rounded-box border border-base-300 p-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-base-content/60 tooltip"
                                    data-tip="One character_achievements row means completed, even when completed_at is zero">
                                    Durable completion
                                </div>
                                @if ($completion)
                                    <div class="mt-2 font-semibold text-success">Completed</div>
                                    <div class="mt-1 text-sm">
                                        @if ((int) $completion->completed_at > 0)
                                            {{ \Carbon\Carbon::createFromTimestamp((int) $completion->completed_at)->format('Y-m-d H:i:s') }}
                                        @else
                                            Timestamp 0
                                        @endif
                                    </div>
                                    <div class="text-xs text-base-content/60 tooltip"
                                        data-tip="Version copied into character_achievements when completion was persisted">
                                        Persisted version {{ $completion->version }}
                                    </div>
                                @else
                                    <div class="mt-2 text-base-content/60">No completion row</div>
                                @endif
                            </div>

                            <div class="rounded-box border border-base-300 p-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-base-content/60 tooltip"
                                    data-tip="Progress rows are keyed by character, achievement, component type, and component ID">
                                    Durable progress
                                </div>
                                <div class="mt-2 text-2xl font-semibold">{{ $achievement->progress->count() }}</div>
                                <div class="text-sm text-base-content/65">
                                    {{ $achievement->progress->where('completed', 1)->count() }} component rows satisfied
                                </div>
                            </div>

                            <div class="rounded-box border border-base-300 p-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-base-content/60 tooltip"
                                    data-tip="Reward ledgers survive ordinary reset to preserve at-most-once delivery">
                                    Recovery state
                                </div>
                                <div class="mt-2 text-sm">
                                    {{ $achievement->reward_ledgers->count() }} reward ledgers ·
                                    {{ $achievement->reward_selections->count() }} selections ·
                                    {{ $achievement->pending_updates->count() }} queued updates
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 rounded-box bg-base-200/60 p-3">
                            <a href="{{ route('achievements.edit', $achievement->id) }}"
                                class="btn btn-sm btn-soft btn-info tooltip"
                                data-tip="Edit the global definition; character state is not changed">Edit Definition</a>

                            @if (!$completion)
                                <form method="POST"
                                    action="{{ route('characters.achievements.complete', [$character->id, $achievement->id]) }}"
                                    onsubmit="return confirm('Force durable completion for this OFFLINE character? This bypasses live notification and immediate reward/dependency side effects. The next character load will reconcile persisted state.')">
                                    @csrf
                                    <input type="hidden" name="confirm_offline_completion" value="1">
                                    <button type="submit"
                                        class="btn btn-sm btn-soft btn-warning tooltip"
                                        data-tip="Writes character_achievements with the current definition version and Unix time"
                                        @disabled($isOnline || !$achievement->enabled)>
                                        Force Complete Offline
                                    </button>
                                </form>
                                @if ($isOnline)
                                    <span class="text-xs text-error">Log out before force-completing.</span>
                                @elseif (!$achievement->enabled)
                                    <span class="text-xs text-error">Disabled definitions are unavailable to runtime state updates.</span>
                                @endif
                            @endif

                            @if ($hasCoreState)
                                <form method="POST"
                                    action="{{ route('characters.achievements.reset', [$character->id, $achievement->id]) }}"
                                    onsubmit="return confirm('Reset completion, component progress, and ALL queued updates for this achievement? Reward selections and reward ledgers will be preserved. This cannot be undone from the editor.')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="confirm_reset" value="1">
                                    <button type="submit" class="btn btn-sm btn-soft btn-error tooltip"
                                        data-tip="Source-equivalent reset; reward safety ledgers remain intact and disabled definitions can still be cleaned up">
                                        Reset State
                                    </button>
                                </form>
                            @endif

                            @if ($hasRewardState)
                                <form method="POST"
                                    action="{{ route('characters.achievements.reset', [$character->id, $achievement->id]) }}"
                                    onsubmit="return confirm('HIGH RISK: Reset completion, progress, queued updates, reward selections, AND individual reward ledgers? Recompletion can grant rewards again and may duplicate previously delivered items or currency.')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="confirm_reset" value="1">
                                    <input type="hidden" name="reset_rewards" value="1">
                                    <button type="submit" class="btn btn-sm btn-error tooltip"
                                        data-tip="Deletes at-most-once reward ledgers; use only when intentional">
                                        Reset State + Rewards
                                    </button>
                                </form>
                            @endif

                            @if (!$hasAnyState)
                                <span class="text-sm text-base-content/60">No mutable durable detail was found.</span>
                            @endif
                        </div>

                        <section class="space-y-3">
                            <div>
                                <h3 class="text-base font-semibold">Components and exact progress</h3>
                                <p class="text-xs text-base-content/65">
                                    Types 0–2 carry state. Type 3 is presentation-only. Exact assignment can lower
                                    progress and is clamped to an enabled criterion's required count, or to the
                                    presentation count when no enabled criterion owns the component. It updates only
                                    the component row; completion, rewards, and dependencies wait for runtime
                                    reconciliation unless you explicitly use offline force-completion.
                                </p>
                            </div>

                            @forelse ($achievement->components as $component)
                                @php
                                    $componentProgress = $component->progress;
                                    $currentCount = (int) ($componentProgress->current_count ?? 0);
                                    $requiredCount = max((int) $component->effective_required_count, 1);
                                    $componentSatisfied = (bool) ($componentProgress->completed ?? false);
                                @endphp
                                <div class="rounded-box border border-base-300 p-3">
                                    <div class="grid grid-cols-1 gap-3 xl:grid-cols-12 xl:items-start">
                                        <div class="xl:col-span-7">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="badge badge-sm badge-outline tooltip"
                                                    data-tip="Wire bucket; this is not criterion behavior">
                                                    {{ $metadata['component_types'][(int) $component->component_type] ?? "Unknown type {$component->component_type}" }}
                                                </span>
                                                <span class="badge badge-sm badge-ghost tooltip"
                                                    data-tip="Stable identity is type + component ID; sequence is display order">
                                                    ID {{ $component->component_id }} · Seq {{ $component->sequence }}
                                                </span>
                                                @if ($componentSatisfied)
                                                    <span class="badge badge-sm badge-success badge-soft tooltip"
                                                        data-tip="Materialized character_achievement_progress.completed flag">
                                                        Satisfied
                                                    </span>
                                                @endif
                                                @if ($component->effective_count_conflict)
                                                    <span class="badge badge-sm badge-error tooltip"
                                                        data-tip="Enabled criteria disagree or contain an invalid zero required count">
                                                        Invalid count policy
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-2 font-medium">
                                                {{ $component->name ?: '(No component name)' }}
                                            </div>
                                            @if ($component->description)
                                                <div class="mt-1 text-sm text-base-content/65">{{ $component->description }}</div>
                                            @endif
                                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-base-content/65">
                                                <span class="tooltip"
                                                    data-tip="Default count from achievement_associations, normalized to at least 1">
                                                    Presentation count: {{ number_format($component->presentation_required_count) }}
                                                </span>
                                                <span class="tooltip"
                                                    data-tip="Runtime threshold after enabled criterion override">
                                                    Effective count: {{ number_format($requiredCount) }}
                                                </span>
                                                <span class="tooltip"
                                                    data-tip="Distinct required_count values from enabled criteria">
                                                    Criterion counts:
                                                    {{ $component->criterion_required_counts ? implode(', ', $component->criterion_required_counts) : 'none' }}
                                                </span>
                                                @if ($componentProgress)
                                                    <span class="tooltip"
                                                        data-tip="Unix updated_at {{ (int) $componentProgress->updated_at }}; persisted sequence {{ $componentProgress->component_sequence }}">
                                                        Row version {{ $componentProgress->version }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="xl:col-span-2">
                                            <div class="text-xs font-semibold uppercase text-base-content/60 tooltip"
                                                data-tip="Durable current_count divided by the effective threshold">Progress</div>
                                            <div class="mt-1 font-mono text-lg">
                                                {{ number_format($currentCount) }} / {{ number_format($requiredCount) }}
                                            </div>
                                            <progress class="progress progress-accent mt-1 w-full"
                                                value="{{ min($currentCount, $requiredCount) }}"
                                                max="{{ $requiredCount }}"></progress>
                                        </div>

                                        <div class="xl:col-span-3">
                                            @if (!$component->state_bearing)
                                                <div role="alert" class="alert alert-soft py-2 text-xs">
                                                    No RoF2 state/count channel exists for type 3.
                                                </div>
                                            @elseif ($completion)
                                                <div role="alert" class="alert alert-soft alert-info py-2 text-xs">
                                                    Reset completion before editing component progress.
                                                </div>
                                            @elseif (!$achievement->enabled)
                                                <div role="alert" class="alert alert-soft alert-error py-2 text-xs">
                                                    Enable the definition before runtime-compatible state updates.
                                                </div>
                                            @elseif ($component->effective_count_conflict)
                                                <div role="alert" class="alert alert-soft alert-error py-2 text-xs">
                                                    Repair the enabled criterion count policy first.
                                                </div>
                                            @else
                                                <form method="POST"
                                                    action="{{ route('characters.achievements.progress.update', [
                                                        $character->id,
                                                        $achievement->id,
                                                        $component->component_type,
                                                        $component->component_id,
                                                    ]) }}"
                                                    onsubmit="return confirm('Set this component to the exact entered count? Exact assignment can LOWER durable progress. The server will clamp it to the effective required count.')">
                                                    @csrf
                                                    @method('PUT')
                                                    <label class="form-control">
                                                        <span class="label py-1">
                                                            <span class="label-text text-xs font-semibold">Exact count</span>
                                                            <span class="label-text-alt tooltip tooltip-left"
                                                                data-tip="Writes count, satisfied flag, current sequence, definition version, and Unix updated_at">?</span>
                                                        </span>
                                                        <div class="join w-full">
                                                            <input type="number" name="current_count" min="0"
                                                                max="{{ $requiredCount }}" value="{{ $currentCount }}"
                                                                class="input input-sm input-bordered join-item min-w-0 flex-1"
                                                                required>
                                                            <button type="submit"
                                                                class="btn btn-sm btn-accent join-item tooltip"
                                                                data-tip="Persist this exact component count">Set</button>
                                                        </div>
                                                    </label>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <details class="mt-3 rounded-box bg-base-200/50 px-3 py-2">
                                        <summary class="cursor-pointer text-sm font-medium tooltip"
                                            data-tip="Criteria define evaluation behavior and override the presentation threshold">
                                            Criteria ({{ $component->criteria->count() }})
                                        </summary>
                                        <div class="mt-2 overflow-x-auto">
                                            <table class="table table-xs">
                                                <thead>
                                                    <tr>
                                                        <th title="achievement_criteria.id">ID</th>
                                                        <th title="Only enabled criteria participate in runtime policy">Enabled</th>
                                                        <th title="Runtime event that supplies observed values">Event</th>
                                                        <th title="How observed values become component counts">Progress mode</th>
                                                        <th title="How this component affects completion, locking, or visibility">Behavior</th>
                                                        <th title="Primary and secondary event target identities">Targets</th>
                                                        <th title="Explicit runtime threshold; must be nonzero">Required</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($component->criteria as $criterion)
                                                        <tr>
                                                            <td class="font-mono">{{ $criterion->id }}</td>
                                                            <td>
                                                                <span class="badge badge-xs {{ $criterion->enabled ? 'badge-success' : 'badge-neutral' }} badge-soft">
                                                                    {{ $criterion->enabled ? 'Yes' : 'No' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{ \App\Support\Achievements\AchievementMetadata::eventLabel((int) $criterion->event_type) }}
                                                            </td>
                                                            <td>
                                                                {{ \App\Support\Achievements\AchievementMetadata::progressModeLabel((int) $criterion->progress_mode) }}
                                                            </td>
                                                            <td>
                                                                {{ \App\Support\Achievements\AchievementMetadata::behaviorLabel((int) $criterion->behavior) }}
                                                            </td>
                                                            <td class="font-mono text-xs">
                                                                {{ $criterion->target_id }} / {{ $criterion->target_id2 }}
                                                                <span class="block text-base-content/60">value {{ $criterion->target_value }}</span>
                                                            </td>
                                                            <td class="font-mono">{{ number_format((int) $criterion->required_count) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center text-base-content/55">
                                                                No evaluation criterion; presentation count is the effective threshold.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </details>
                                </div>
                            @empty
                                <div role="alert" class="alert alert-soft alert-warning">
                                    This definition has no components. Enabled runtime content with invalid structure fails closed.
                                </div>
                            @endforelse
                        </section>

                        <section class="space-y-3">
                            <div>
                                <h3 class="text-base font-semibold">Individual reward ledgers</h3>
                                <p class="text-xs text-base-content/65">
                                    Status 0 is claimed/in flight and may be ambiguous after interruption; status 1 is
                                    durably granted; only status 2 is automatically retryable. Automatic grants may be
                                    overridden individually. Selectable grants must be retried through their owning
                                    selection so selected and common rows are reconciled together.
                                </p>
                            </div>

                            @if ($achievement->rewards->isNotEmpty() || $orphanRewardLedgers->isNotEmpty())
                                <div class="overflow-x-auto rounded-box border border-base-300">
                                    <table class="table table-sm">
                                        <thead class="bg-base-200">
                                            <tr>
                                                <th title="Canonical rewards.reward_id">Reward</th>
                                                <th title="Grant kind and authored data identity">Definition</th>
                                                <th title="Character-scoped idempotency ledger status">Ledger status</th>
                                                <th title="Delivery claims started and latest attempt diagnostic">Attempts / diagnostic</th>
                                                <th class="text-right" title="Explicitly override an ambiguous or failed row to retryable">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($achievement->rewards as $reward)
                                                @php
                                                    $ledger = $reward->ledger;
                                                    $ledgerStatus = $ledger ? (int) $ledger->status : null;
                                                    $statusClass = match ($ledgerStatus) {
                                                        1 => 'badge-success',
                                                        2 => 'badge-warning',
                                                        0 => 'badge-error',
                                                        default => 'badge-ghost',
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="font-mono">{{ $reward->reward_id }}</span>
                                                        <span class="block text-xs text-base-content/60 tooltip"
                                                            data-tip="Sequence is scoped to the automatic source or selected option">
                                                            {{ $reward->delivery }} · sequence {{ $reward->sequence }} · {{ $reward->enabled ? 'enabled' : 'disabled' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="font-medium">
                                                            {{ \App\Support\Achievements\AchievementMetadata::rewardTypeLabel((int) $reward->reward_type) }}
                                                        </span>
                                                        <span class="block text-xs text-base-content/65">
                                                            data {{ $reward->reward_data_id }} · amount {{ $reward->amount }}
                                                        </span>
                                                        @if ($reward->description)
                                                            <span class="block text-xs">{{ $reward->description }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($ledger)
                                                            <span class="badge badge-sm {{ $statusClass }} badge-soft tooltip"
                                                                data-tip="{{ $metadata['reward_statuses'][$ledgerStatus] ?? 'Unknown ledger status' }}">
                                                                {{ $metadata['reward_statuses'][$ledgerStatus] ?? "Unknown ({$ledgerStatus})" }}
                                                            </span>
                                                            @if ((int) $ledger->granted_at > 0)
                                                                <span class="mt-1 block text-xs tooltip"
                                                                    data-tip="Unix granted_at {{ $ledger->granted_at }}">
                                                                    {{ \Carbon\Carbon::createFromTimestamp((int) $ledger->granted_at)->format('Y-m-d H:i:s') }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-sm badge-ghost tooltip"
                                                                data-tip="No character_achievement_rewards row exists">Not claimed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($ledger)
                                                            <span class="font-mono">{{ $ledger->attempt_count }}</span>
                                                            @if ($ledger->last_error)
                                                                <span class="mt-1 block max-w-sm break-words text-xs text-error tooltip"
                                                                    data-tip="Last persisted delivery diagnostic">{{ $ledger->last_error }}</span>
                                                            @else
                                                                <span class="block text-xs text-base-content/50">No diagnostic</span>
                                                            @endif
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @if ($ledger && $reward->delivery === 'automatic' && in_array($ledgerStatus, [0, 2], true))
                                                            <form method="POST"
                                                                action="{{ route('characters.achievements.rewards.retry', [
                                                                    $character->id,
                                                                    $achievement->id,
                                                                    $reward->reward_id,
                                                                ]) }}"
                                                                onsubmit="return confirm('Mark this ONE reward ledger retryable? If status 0 was already delivered before interruption, retrying can duplicate the grant. Continue only after checking the character inventory/currency and accepting that risk.')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="confirm_retry" value="1">
                                                                <button type="submit"
                                                                    class="btn btn-xs btn-warning tooltip"
                                                                    data-tip="Set only this ledger status to explicit retryable failure">
                                                                    Mark Retryable
                                                                </button>
                                                            </form>
                                                        @elseif ($ledger && $reward->delivery === 'selectable' && in_array($ledgerStatus, [0, 2], true))
                                                            <span class="text-xs text-info tooltip"
                                                                data-tip="Use the owning selection's retry action so selected and common grants move together">
                                                                Retry selection
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-base-content/45 tooltip"
                                                                data-tip="Granted rows are protected; missing rows have nothing to retry">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @foreach ($orphanRewardLedgers as $ledger)
                                                @php
                                                    $ledgerStatus = (int) $ledger->status;
                                                @endphp
                                                <tr class="bg-error/5">
                                                    <td class="font-mono">{{ $ledger->reward_id }}</td>
                                                    <td>
                                                        <span class="badge badge-error badge-outline tooltip"
                                                            data-tip="The canonical rewards row is missing or no longer maps to this achievement source">
                                                            Orphan ledger
                                                        </span>
                                                    </td>
                                                    <td>{{ $metadata['reward_statuses'][$ledgerStatus] ?? "Unknown ({$ledgerStatus})" }}</td>
                                                    <td>
                                                        {{ $ledger->attempt_count }} attempts
                                                        @if ($ledger->last_error)
                                                            <span class="block text-xs text-error">{{ $ledger->last_error }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right text-xs text-base-content/55">
                                                        Restore or correct reward content before retrying.
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="rounded-box border border-dashed border-base-300 p-3 text-sm text-base-content/55">
                                    No mapped reward definitions or individual ledgers.
                                </div>
                            @endif
                        </section>

                        <section class="space-y-3">
                            <div>
                                <h3 class="text-base font-semibold">Selectable reward sets</h3>
                                <p class="text-xs text-base-content/65">
                                    A selection row locks one option for this achievement and set. Status 3 is
                                    ambiguous and never retries automatically. Individual entries remain protected by
                                    the reward ledgers above.
                                </p>
                            </div>

                            @forelse ($achievement->reward_sets as $set)
                                @php
                                    $selection = $set->selection;
                                    $selectionStatus = $selection ? (int) $selection->status : null;
                                    $selectedOption = $selection
                                        ? $set->options->firstWhere('option_id', $selection->selected_option_id)
                                        : null;
                                    $canRetrySelection = $selection
                                        && (int) $set->source_enabled === 1
                                        && (int) $set->enabled === 1
                                        && (int) $selection->selected_option_id !== 0
                                        && $selectedOption
                                        && (int) $selectedOption->enabled === 1
                                        && (int) $selectedOption->common_to_all === 0
                                        && in_array($selectionStatus, [0, 2, 3], true);
                                @endphp
                                <div class="rounded-box border border-base-300 p-3">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-semibold">{{ $set->title ?: 'Selectable reward' }}</span>
                                                <span class="badge badge-sm badge-outline tooltip"
                                                    data-tip="reward_sets.reward_set_id">Set {{ $set->reward_set_id }}</span>
                                                <span class="badge badge-sm {{ $set->enabled ? 'badge-info' : 'badge-neutral' }} badge-soft tooltip"
                                                    data-tip="Only enabled sets and enabled options are loaded">
                                                    {{ $set->enabled ? 'Enabled' : 'Disabled' }}
                                                </span>
                                                <span class="badge badge-sm {{ $set->source_enabled ? 'badge-info' : 'badge-neutral' }} badge-outline tooltip"
                                                    data-tip="This achievement's independent reward_sources.enabled state">
                                                    Source {{ $set->source_enabled ? 'enabled' : 'disabled' }}
                                                </span>
                                            </div>
                                            @if ($selection)
                                                <div class="mt-2 text-sm">
                                                    <span class="font-medium">Selection:</span>
                                                    {{ $metadata['selection_statuses'][$selectionStatus] ?? "Unknown ({$selectionStatus})" }} ·
                                                    option {{ $selection->selected_option_id ?: 'not chosen' }}
                                                    @if ($selectedOption)
                                                        ({{ $selectedOption->label ?: 'unlabeled' }})
                                                    @endif
                                                    · {{ $selection->attempt_count }} attempts
                                                </div>
                                                @if ($selection->last_error)
                                                    <div class="mt-1 max-w-2xl break-words text-xs text-error tooltip"
                                                        data-tip="Last selection-level delivery diagnostic">
                                                        {{ $selection->last_error }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="mt-2 text-sm text-base-content/55">
                                                    No character selection ledger. Completion may create a pending choice on load.
                                                </div>
                                            @endif
                                        </div>

                                        @if ($canRetrySelection)
                                            <form method="POST"
                                                action="{{ route('characters.achievements.reward-selections.retry', [
                                                    $character->id,
                                                    $achievement->id,
                                                    $set->reward_set_id,
                                                ]) }}"
                                                onsubmit="return confirm('Mark this reward selection retryable? The selected option and enabled common grants are validated, and their in-flight individual ledgers are moved to retryable failure atomically. Some grants may already have been delivered, so inspect the character and accept duplicate-delivery risk before continuing.')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="confirm_retry" value="1">
                                                <button type="submit" class="btn btn-xs btn-warning tooltip"
                                                    data-tip="Validate the active set and atomically mark the selection plus relevant in-flight grant ledgers retryable">
                                                    Mark Selection Retryable
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="mt-3 grid grid-cols-1 gap-2 lg:grid-cols-2">
                                        @forelse ($set->options as $option)
                                            <div class="rounded-box bg-base-200/60 p-3">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-medium">{{ $option->label ?: "Option {$option->option_id}" }}</span>
                                                    <span class="badge badge-xs badge-outline tooltip"
                                                        data-tip="Option IDs are scoped to this reward set">{{ $option->option_id }}</span>
                                                    @if ($option->common_to_all)
                                                        <span class="badge badge-xs badge-info tooltip"
                                                            data-tip="This option's entries are delivered with every selectable choice">Common</span>
                                                    @endif
                                                    @if (!$option->enabled)
                                                        <span class="badge badge-xs badge-neutral tooltip"
                                                            data-tip="Disabled options are not loaded by the runtime">Disabled</span>
                                                    @endif
                                                </div>
                                                <div class="mt-2 space-y-1 text-xs">
                                                    @forelse ($option->entries as $entry)
                                                        <div class="flex items-start justify-between gap-2 border-t border-base-300 pt-1 first:border-0 first:pt-0">
                                                            <span>
                                                                Reward {{ $entry->reward_id }}
                                                                @if ($entry->reward)
                                                                    · {{ \App\Support\Achievements\AchievementMetadata::rewardTypeLabel((int) $entry->reward->reward_type) }}
                                                                    @if ($entry->reward->description)
                                                                        · {{ $entry->reward->description }}
                                                                    @endif
                                                                @else
                                                                    <span class="text-error">· missing canonical reward</span>
                                                                @endif
                                                            </span>
                                                            @if ($entry->reward?->ledger)
                                                                <span class="badge badge-xs badge-outline tooltip"
                                                                    data-tip="Individual entry ledger status">
                                                                    {{ $metadata['reward_statuses'][(int) $entry->reward->ledger->status] ?? 'Unknown' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <div class="text-base-content/50">No mapped reward entries.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-sm text-error">
                                                No options. An enabled selectable set without usable choices is invalid runtime content.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                @if ($orphanSelections->isEmpty())
                                    <div class="rounded-box border border-dashed border-base-300 p-3 text-sm text-base-content/55">
                                        No selectable reward set or selection ledger.
                                    </div>
                                @endif
                            @endforelse

                            @if ($orphanSelections->isNotEmpty())
                                <div role="alert" class="alert alert-soft alert-error items-start">
                                    <div>
                                        <div class="font-semibold">Orphan reward selections</div>
                                        @foreach ($orphanSelections as $selection)
                                            <div class="mt-1 text-sm">
                                                Set {{ $selection->reward_set_id }} · option {{ $selection->selected_option_id }} ·
                                                {{ $metadata['selection_statuses'][(int) $selection->status] ?? "Unknown ({$selection->status})" }}
                                            </div>
                                        @endforeach
                                        <p class="mt-1 text-xs">
                                            The authored set is missing or no longer belongs to this achievement. Repair content before retrying.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </section>

                        <section class="space-y-3">
                            <div>
                                <h3 class="text-base font-semibold">Pending cross-zone updates</h3>
                                <p class="text-xs text-base-content/65">
                                    Advance requests are monotonic floors, not exact assignments. Blocked rows retain
                                    diagnostics. Retrying clears status, lease timestamp, and diagnostic but preserves
                                    the attempt count and original definition-version guard.
                                </p>
                            </div>

                            @if ($achievement->pending_updates->isNotEmpty())
                                <div class="overflow-x-auto rounded-box border border-base-300">
                                    <table class="table table-sm">
                                        <thead class="bg-base-200">
                                            <tr>
                                                <th title="Auto-increment queue identity">Update</th>
                                                <th title="Original character/group/raid/dynamic-zone/shared-task scope">Source</th>
                                                <th title="Operation 0 advances a floor; operation 1 completes">Request</th>
                                                <th title="Definition version captured by the source zone; exact match is required">Version</th>
                                                <th title="Pending, blocked, or processing under a 60-second lease">Status</th>
                                                <th title="Claim count and most recent diagnostic">Attempts / diagnostic</th>
                                                <th class="text-right" title="Retry a blocked row or explicitly discard the authored request">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($achievement->pending_updates as $update)
                                                @php
                                                    $updateStatus = (int) $update->status;
                                                    $updateOperation = (int) $update->operation;
                                                    $updateStatusClass = match ($updateStatus) {
                                                        0 => 'badge-info',
                                                        1 => 'badge-error',
                                                        2 => 'badge-warning',
                                                        default => 'badge-neutral',
                                                    };
                                                @endphp
                                                <tr class="{{ (int) $update->version !== (int) $achievement->version ? 'bg-error/5' : '' }}">
                                                    <td>
                                                        <span class="font-mono">{{ $update->update_id }}</span>
                                                        <span class="block text-xs text-base-content/60 tooltip"
                                                            data-tip="Unix created_at {{ $update->created_at }}">
                                                            {{ \Carbon\Carbon::createFromTimestamp((int) $update->created_at)->format('Y-m-d H:i:s') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $metadata['update_target_types'][(int) $update->source_target_type] ?? "Unknown ({$update->source_target_type})" }}
                                                        <span class="block font-mono text-xs">{{ $update->source_target_id }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="font-medium">
                                                            {{ $metadata['update_operations'][$updateOperation] ?? "Unknown ({$updateOperation})" }}
                                                        </span>
                                                        @if ($updateOperation === \App\Support\Achievements\AchievementMetadata::UPDATE_OPERATION_ADVANCE)
                                                            <span class="block font-mono text-xs">
                                                                type {{ $update->component_type }} · component {{ $update->component_id }} · floor {{ $update->requested_value }}
                                                            </span>
                                                        @else
                                                            <span class="block text-xs text-base-content/60">Whole achievement</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="font-mono">{{ $update->version }}</span>
                                                        @if ((int) $update->version !== (int) $achievement->version)
                                                            <span class="block text-xs text-error tooltip"
                                                                data-tip="Retrying without correcting content/version will block again">
                                                                current {{ $achievement->version }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-sm {{ $updateStatusClass }} badge-soft tooltip"
                                                            data-tip="Processing rows become reclaimable after {{ $metadata['update_processing_lease_seconds'] }} seconds">
                                                            {{ $metadata['update_statuses'][$updateStatus] ?? "Unknown ({$updateStatus})" }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="font-mono">{{ $update->attempt_count }}</span>
                                                        @if ($update->last_error)
                                                            <span class="mt-1 block max-w-sm break-words text-xs text-error tooltip"
                                                                data-tip="Most recent blocked or retryable diagnostic">
                                                                {{ $update->last_error }}
                                                            </span>
                                                        @else
                                                            <span class="block text-xs text-base-content/50">No diagnostic</span>
                                                        @endif
                                                        @if ((int) $update->last_attempt_at > 0)
                                                            <span class="block text-xs text-base-content/55 tooltip"
                                                                data-tip="Unix last_attempt_at {{ $update->last_attempt_at }}">
                                                                last {{ \Carbon\Carbon::createFromTimestamp((int) $update->last_attempt_at)->format('Y-m-d H:i:s') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="flex justify-end gap-1">
                                                            @if ($updateStatus === \App\Support\Achievements\AchievementMetadata::CHARACTER_UPDATE_STATUS_BLOCKED)
                                                                <form method="POST"
                                                                    action="{{ route('characters.achievements.updates.retry', [
                                                                        $character->id,
                                                                        $achievement->id,
                                                                        $update->update_id,
                                                                    ]) }}"
                                                                    onsubmit="return confirm('Retry this blocked update? Its original definition version and requested value are preserved. If the underlying content problem remains, it will block again.')">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                        class="btn btn-xs btn-soft btn-warning tooltip"
                                                                        data-tip="Reset status to pending and clear lease timestamp/diagnostic">
                                                                        Retry
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            @if (in_array($updateStatus, [
                                                                \App\Support\Achievements\AchievementMetadata::CHARACTER_UPDATE_STATUS_PENDING,
                                                                \App\Support\Achievements\AchievementMetadata::CHARACTER_UPDATE_STATUS_BLOCKED,
                                                            ], true))
                                                                <form method="POST"
                                                                    action="{{ route('characters.achievements.updates.discard', [
                                                                        $character->id,
                                                                        $achievement->id,
                                                                        $update->update_id,
                                                                    ]) }}"
                                                                    onsubmit="return confirm('Discard this queued update permanently? The authored cross-zone request will be deleted WITHOUT applying its progress or completion. This cannot be undone.')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="confirm_discard" value="1">
                                                                    <button type="submit" class="btn btn-xs btn-error tooltip"
                                                                        data-tip="Delete only this pending or blocked character/achievement/update tuple">
                                                                        Discard
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="text-xs text-warning tooltip"
                                                                    data-tip="The runtime owns this row while its processing lease is active">
                                                                    Runtime lease active
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="rounded-box border border-dashed border-base-300 p-3 text-sm text-base-content/55">
                                    No pending, blocked, or leased updates for this character and achievement.
                                </div>
                            @endif
                        </section>
                    </div>
                </details>
            @empty
                <div class="rounded-box border border-dashed border-base-300 bg-base-100 p-8 text-center">
                    <div class="font-semibold">No achievements match these filters.</div>
                    <p class="mt-1 text-sm text-base-content/60">Clear one or more filters to return to the full catalog.</p>
                </div>
            @endforelse
        </div>

        @if ($achievements->hasPages())
            <div class="mt-4">{{ $achievements->links() }}</div>
        @endif
    </div>
@endsection
