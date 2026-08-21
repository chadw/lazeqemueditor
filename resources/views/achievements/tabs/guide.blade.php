@php
    $eventGuidance = $metadata['target_guidance'] ?? [];
    $allowedModes = $metadata['allowed_progress_modes'] ?? [];
@endphp
<div class="space-y-4">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm xl:col-span-2">
            <div class="card-body">
                <h2 class="card-title">Safe Publishing Workflow</h2>
                <ol class="list-decimal ml-5 space-y-2 text-sm">
                    <li>Author new or incompatible work as a disabled draft and use new stable IDs for new durable identities.</li>
                    <li>Verify category ancestry, component policy consistency, referenced records, rewards, and dependency cycles.</li>
                    <li>For deployed semantic changes—including criteria, reward grants, the selectable source link, or reset behavior—explicitly increment <code>version</code> and choose whether mismatched character state and reward ledgers should reset. Version <code>0</code> is valid initially.</li>
                    <li>Save the transaction, review the Validation tab, then run <code>#reload achievements global</code>.</li>
                    <li>Read the zone result. Reload stages and validates the full catalog; a failed load leaves the previous active snapshot in place.</li>
                </ol>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Durable Identity Rules</h2>
                <ul class="list-disc ml-5 space-y-2 text-sm">
                    <li>Achievement and category IDs are immutable after creation.</li>
                    <li>Component IDs key durable progress and a global presentation count.</li>
                    <li>Reward and reward-set IDs key idempotent character delivery ledgers.</li>
                    <li>Mapped grants never become automatic merely because an option is disabled.</li>
                    <li>Safe Clone creates a disabled definition with new reward identities and no cast restrictions.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Criterion Event Reference</h2>
            <p class="text-sm opacity-65">Target help in the Components tab changes immediately when an event is selected.</p>
            <div class="overflow-x-auto mt-2">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Primary target</th>
                            <th>Secondary target</th>
                            <th>Value / replay behavior</th>
                            <th>Allowed modes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($metadata['event_types'] as $eventId => $eventLabel)
                            @php
                                $guidance = $eventGuidance[$eventId] ?? [];
                                $modeLabels = collect($allowedModes[$eventId] ?? [])
                                    ->map(fn($mode) => $metadata['progress_modes'][$mode] ?? $mode)
                                    ->implode(', ');
                            @endphp
                            <tr>
                                <td class="font-medium whitespace-nowrap"><span class="badge badge-ghost mr-1">{{ $eventId }}</span>{{ $eventLabel }}</td>
                                <td>
                                    <div class="font-medium">{{ $guidance['target_id_label'] ?? 'Target ID' }}</div>
                                    <div class="text-xs opacity-65">{{ $guidance['target_id_help'] ?? '' }}</div>
                                </td>
                                <td>
                                    <div class="font-medium">{{ $guidance['target_id2_label'] ?? 'Secondary Target' }}</div>
                                    <div class="text-xs opacity-65">{{ $guidance['target_id2_help'] ?? '' }}</div>
                                </td>
                                <td>
                                    <div>{{ $guidance['target_value_help'] ?? '' }}</div>
                                    <div class="text-xs text-info mt-1">{{ $guidance['replay'] ?? '' }}</div>
                                </td>
                                <td class="text-xs">{{ $modeLabels ?: 'None' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Component Behaviors</h2>
                <dl class="grid grid-cols-[max-content_1fr] gap-x-4 gap-y-2 text-sm">
                    <dt><strong>Required</strong></dt><dd>Contributes to normal completion.</dd>
                    <dt><strong>Optional</strong></dt><dd>Tracks and displays without affecting completion.</dd>
                    <dt><strong>Unlock</strong></dt><dd>Keeps the achievement Locked while this component is unsatisfied.</dd>
                    <dt><strong>Visibility</strong></dt><dd>Keeps the achievement Hidden while this component is unsatisfied.</dd>
                    <dt><strong>Display Only</strong></dt><dd>Presents state without contributing to completion.</dd>
                    <dt><strong>Blocker</strong></dt><dd>Prevents completion while its blocking condition is active.</dd>
                </dl>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Reward Data IDs</h2>
                <dl class="grid grid-cols-[max-content_1fr] gap-x-4 gap-y-2 text-sm">
                    <dt><strong>Item</strong></dt><dd><code>items.id</code>; amount is stack count.</dd>
                    <dt><strong>Experience</strong></dt><dd>Data 0 is normal handling; data 1 is normal-only raw XP.</dd>
                    <dt><strong>AA points</strong></dt><dd>Data 0; amount is the awarded AA quantity.</dd>
                    <dt><strong>Copper</strong></dt><dd>Data 0; amount is the copper quantity.</dd>
                    <dt><strong>Alt currency</strong></dt><dd>Data is the alternate currency ID.</dd>
                    <dt><strong>Title set</strong></dt><dd>Data is <code>titles.title_set</code>.</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
