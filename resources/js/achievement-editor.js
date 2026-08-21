const clone = (value) => JSON.parse(JSON.stringify(value ?? {}));

const number = (value, fallback = 0) => {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : fallback;
};

export default function registerAchievementEditor(Alpine) {
    Alpine.data('achievementEditor', (config = {}) => ({
        tab: 'general',
        editor: clone(config.editor),
        metadata: clone(config.metadata),
        initialDefinitionVersion: 0,
        nextUid: 1,

        init() {
            this.editor.associations = Array.isArray(this.editor.associations)
                ? this.editor.associations
                : [];
            this.editor.components = Array.isArray(this.editor.components)
                ? this.editor.components
                : [];
            this.editor.rewards = Array.isArray(this.editor.rewards)
                ? this.editor.rewards
                : [];
            this.editor.restrictions = Array.isArray(this.editor.restrictions)
                ? this.editor.restrictions
                : [];

            const suggestedSetId = number(
                this.editor.suggested_reward_set_id
                    ?? config.suggestedRewardSetId
                    ?? this.editor.id,
                1,
            );
            if (!this.editor.reward_set) {
                this.editor.reward_set = {
                    present: false,
                    reward_set_id: suggestedSetId,
                    title: '',
                    enabled: 1,
                    source_enabled: 1,
                    options: [],
                };
            } else {
                this.editor.reward_set.present = this.editor.reward_set.present ?? true;
                this.editor.reward_set.options = Array.isArray(this.editor.reward_set.options)
                    ? this.editor.reward_set.options
                    : [];
            }

            this.initialDefinitionVersion = number(this.editor.version, 0);
            this.decorateRows();

            if (this.editor.associations.length === 0 && number(this.editor.enabled) === 1) {
                this.addAssociation();
            }
        },

        decorateRows() {
            const decorate = (row) => {
                if (!row._uid) row._uid = this.uid();
                return row;
            };

            this.editor.associations.forEach(decorate);
            this.editor.components.forEach((component) => {
                decorate(component);
                component.criteria = Array.isArray(component.criteria) ? component.criteria : [];
                component.criteria.forEach((criterion) => {
                    decorate(criterion);
                    criterion.target_name = criterion.target_name ?? '';
                    criterion._eventType = number(criterion.event_type);
                });
            });
            this.editor.rewards.forEach((reward) => {
                decorate(reward);
                reward._rewardType = number(reward.reward_type);
            });
            this.editor.restrictions.forEach(decorate);
            this.editor.reward_set.options.forEach(decorate);
        },

        uid() {
            return `achievement-row-${Date.now()}-${this.nextUid++}`;
        },

        addAssociation() {
            this.editor.associations.push({
                _uid: this.uid(),
                category_id: '',
                sequence: this.nextSequence(this.editor.associations),
                display_text: '',
            });
        },

        addComponent() {
            this.editor.components.push({
                _uid: this.uid(),
                component_type: 1,
                sequence: this.nextSequence(this.editor.components),
                component_id: this.nextComponentId(),
                name: '',
                description: '',
                presentation_count: 1,
                criteria: [],
            });
        },

        addCriterion(component) {
            component.criteria.push({
                _uid: this.uid(),
                id: null,
                event_type: 0,
                progress_mode: 2,
                behavior: 0,
                target_id: 0,
                target_id2: 0,
                target_value: 0,
                required_count: Math.max(1, number(component.presentation_count, 1)),
                enabled: 1,
                target_name: '',
            });
        },

        addReward() {
            this.editor.rewards.push({
                _uid: this.uid(),
                reward_id: null,
                sequence: this.nextSequence(this.editor.rewards),
                reward_type: 0,
                reward_data_id: 0,
                amount: 1,
                description: '',
                enabled: 1,
                option_id: '',
            });
        },

        addRewardOption() {
            this.editor.reward_set.present = true;
            this.editor.reward_set.options.push({
                _uid: this.uid(),
                option_id: this.nextOptionId(),
                sequence: this.nextSequence(this.editor.reward_set.options),
                label: '',
                common_to_all: 0,
                flags: 0,
                enabled: 1,
            });
        },

        removeRewardOption(index) {
            const option = this.editor.reward_set.options[index];
            if (option) {
                this.editor.rewards.forEach((reward) => {
                    if (String(reward.option_id) === String(option.option_id)) {
                        reward.option_id = '';
                        reward.enabled = 0;
                    }
                });
            }
            this.editor.reward_set.options.splice(index, 1);
        },

        addRestriction() {
            this.editor.restrictions.push({
                _uid: this.uid(),
                restriction_id: 0,
                requires_completed: 1,
            });
        },

        remove(list, index) {
            list.splice(index, 1);
        },

        nextSequence(rows) {
            if (!rows.length) return 0;
            return Math.max(...rows.map((row) => number(row.sequence, 0))) + 1;
        },

        nextComponentId() {
            const ids = this.editor.components.map((row) => number(row.component_id, 0));
            const suggested = Math.max(1, number(this.editor.suggested_component_id, 1));
            return Math.max(suggested - 1, ...ids, 0) + 1;
        },

        nextOptionId() {
            const ids = this.editor.reward_set.options.map((row) => number(row.option_id, 0));
            return Math.max(...ids, 0) + 1;
        },

        lookupType(eventType, target = 1) {
            const event = number(eventType);
            if (target === 2) {
                return event === 12 ? 'zone' : null;
            }

            return {
                2: 'npc',
                4: 'task',
                5: 'zone',
                6: 'item',
                7: 'item',
                8: 'recipe',
                11: 'achievement',
            }[event] ?? null;
        },

        rewardLookupType(rewardType) {
            return {
                0: 'item',
                4: 'currency',
                5: 'title-set',
            }[number(rewardType)] ?? null;
        },

        targetLabel(eventType, target = 1) {
            const event = number(eventType);
            if (target === 2) {
                return {
                    7: 'Required class (0 = any)',
                    12: 'Zone',
                    13: 'Required class',
                }[event] ?? 'Secondary target (normally 0)';
            }

            return {
                0: 'Target (normally 0)',
                1: 'Target (must be 0)',
                2: 'NPC type (0 = any)',
                3: 'NPC race (0 = any)',
                4: 'Task',
                5: 'Zone (0 = any)',
                6: 'Item (0 = any)',
                7: 'Owned item (0 = per-item wildcard)',
                8: 'Recipe (0 = any)',
                9: 'Skill (4294967295 = any)',
                10: 'Target (must be 0)',
                11: 'Prerequisite achievement (0 = any)',
                12: 'Canonical NPC-name hash',
                13: 'Skill',
            }[event] ?? 'Target';
        },

        targetValueLabel(eventType) {
            return number(eventType) === 13
                ? 'Milestone level'
                : 'Minimum observed value';
        },

        targetHelp(eventType, target = 1) {
            const guidance = this.metadata.target_guidance?.[number(eventType)];
            if (guidance) {
                return target === 2
                    ? (guidance.target_id2_help ?? 'Secondary event discriminator.')
                    : (guidance.target_id_help ?? 'Primary event discriminator.');
            }

            if (target === 2) {
                return {
                    7: 'Optional EQ class ID. Use 0 when item ownership is class-neutral.',
                    12: 'Optional zone scope. Use 0 to match this NPC name in every zone.',
                    13: 'Required EQ class used to resolve the authoritative skill cap.',
                }[number(eventType)] ?? 'This event does not use a secondary target and requires 0.';
            }

            return {
                0: 'Manual criteria normally use 0; scripts address the component identity directly.',
                1: 'Level criteria require 0 because the observed level is supplied by the server.',
                2: 'Choose npc_types.id, or 0 to match any killed NPC type.',
                3: 'Choose the base NPC race, or 0 to match any race.',
                4: 'Choose one exact nonzero tasks.id; task wildcards are rejected.',
                5: 'Choose a zone ID, or 0 to match entry into any zone.',
                6: 'Choose items.id, or 0 to match any item looted from an NPC corpse.',
                7: 'Choose items.id, or 0 for the greatest owned quantity of any one item ID.',
                8: 'Choose tradeskill_recipe.id, or 0 to match any successful recipe.',
                9: 'Choose a skill ID. Skill 0 is valid; 4294967295 is the any-skill wildcard.',
                10: 'Alternate-advancement criteria require 0; the observed spent AA is supplied by the server.',
                11: 'Choose a prerequisite achievement, or 0 to match any durable completion.',
                12: 'Unsigned FNV-1a identity of the canonical ASCII NPC name. Use the helper to avoid hash mistakes.',
                13: 'Choose the exact skill whose DB-backed cap is evaluated.',
            }[number(eventType)] ?? 'Primary event discriminator.';
        },

        targetValueHelp(eventType) {
            const guidance = this.metadata.target_guidance?.[number(eventType)];
            return guidance?.target_value_help
                ?? (number(eventType) === 13
                    ? 'Level 1 through 255 at which the server resolves the DB-backed class/skill cap.'
                    : 'Minimum qualifying event value; Boolean absolute events require a positive threshold.');
        },

        rewardDataHelp(rewardType) {
            return {
                0: 'Required items.id for an item grant.',
                1: '0 uses normal XP handling; 1 grants normal-only raw XP.',
                2: 'AA rewards do not use a referenced data ID; keep this at 0.',
                3: 'Copper rewards do not use a referenced data ID; keep this at 0.',
                4: 'Required alternate currency ID.',
                5: 'Required title_sets.id unlocked by this grant.',
            }[number(rewardType)] ?? 'Reward-specific data identity.';
        },

        eventHint(eventType) {
            return {
                0: 'Manual criteria are advanced by Lua, Perl, or GM operations.',
                1: 'Replayed from the character\'s current level.',
                2: 'Adds credit for a specific NPC type kill; there is no historical replay.',
                3: 'Adds credit for a base NPC race kill; there is no historical replay.',
                4: 'Requires an exact task ID and replays durable completed-task history.',
                5: 'Evaluates the destination zone and reconciles only the current zone. Increment mode is not accepted.',
                6: 'Counts quantities transferred successfully from NPC corpses.',
                7: 'Reconciles durable inventory, bank, shared bank, keyring, augments, bags, and cursor state.',
                8: 'Counts successful combines for the selected recipe.',
                9: 'Reconciles a persisted raw skill value; skill 0 is valid, so use 4294967295 for any skill.',
                10: 'Reconciles purchased-rank cost plus durable expended AA.',
                11: 'Replays durable completion of a prerequisite achievement.',
                12: 'Uses a canonical ASCII name hash and an optional zone scope; there is no historical replay.',
                13: 'Compares a class/skill against the DB-backed cap at the milestone level.',
            }[number(eventType)] ?? '';
        },

        applyEventDefaults(criterion) {
            const event = number(criterion.event_type);
            const previousEvent = number(criterion._eventType, event);
            if (previousEvent !== event) {
                criterion.target_id = 0;
                criterion.target_id2 = 0;
                criterion.target_name = '';
            }
            criterion.target_id2 = [7, 12, 13].includes(event) ? number(criterion.target_id2) : 0;

            if ([1, 10].includes(event)) criterion.target_id = 0;
            if ([1, 4, 5, 7, 9, 10, 11, 13].includes(event) && number(criterion.progress_mode) === 0) {
                criterion.progress_mode = 3;
            }
            if ([1, 7, 9, 10, 13].includes(event) && number(criterion.target_value) < 1) {
                criterion.target_value = 1;
            }
            if (event === 13 && number(criterion.target_id2) < 1) {
                criterion.target_id2 = 1;
            }
            criterion._eventType = event;
        },

        applyRewardTypeDefaults(reward) {
            const rewardType = number(reward.reward_type);
            if (number(reward._rewardType, rewardType) !== rewardType) {
                reward.reward_data_id = 0;
            }
            if ([2, 3].includes(rewardType)) {
                reward.reward_data_id = 0;
            }
            if (number(reward.amount) < 1) {
                reward.amount = 1;
            }
            reward._rewardType = rewardType;
        },

        canonicalNpcName(value) {
            let canonical = '';
            let pendingSpace = false;
            for (const character of String(value ?? '')) {
                const code = character.charCodeAt(0);
                if (code >= 65 && code <= 90) {
                    if (pendingSpace && canonical.length) canonical += ' ';
                    canonical += character.toLowerCase();
                    pendingSpace = false;
                } else if (code >= 97 && code <= 122) {
                    if (pendingSpace && canonical.length) canonical += ' ';
                    canonical += character;
                    pendingSpace = false;
                } else if ((character === ' ' || character === '_') && canonical.length) {
                    pendingSpace = true;
                }
            }
            return canonical;
        },

        npcNameHash(value) {
            const canonical = this.canonicalNpcName(value);
            let hash = 0x811c9dc5;
            for (let index = 0; index < canonical.length; index += 1) {
                hash ^= canonical.charCodeAt(index);
                hash = Math.imul(hash, 0x01000193) >>> 0;
            }
            return canonical ? hash >>> 0 : 0;
        },

        applyNpcName(criterion) {
            criterion.target_id = this.npcNameHash(criterion.target_name);
        },

        optionLabel(option) {
            const prefix = number(option.common_to_all) === 1 ? 'Common' : 'Choice';
            return `${prefix} ${option.option_id}: ${option.label || 'Untitled option'}`;
        },

        validationIssues() {
            const issues = [];
            const push = (level, message) => issues.push({ level, message });
            const utf8Bytes = (value) => new TextEncoder().encode(String(value ?? '')).length;

            if (utf8Bytes(this.editor.description) > 65535) {
                push('error', 'Achievement description exceeds the MySQL TEXT limit of 65,535 UTF-8 bytes.');
            }

            if (!this.editor.associations.length || this.editor.associations.some((row) => !number(row.category_id))) {
                push('error', 'Every enabled definition needs at least one valid category association.');
            }

            const componentKeys = new Set();
            const componentCounts = new Map();
            let requiredCriteria = 0;
            this.editor.components.forEach((component) => {
                const key = `${number(component.component_type)}:${number(component.component_id)}`;
                if (componentKeys.has(key)) push('error', `Duplicate component identity ${key}.`);
                componentKeys.add(key);

                const componentId = number(component.component_id);
                const presentationCount = number(component.presentation_count, 1);
                if (componentCounts.has(componentId) && componentCounts.get(componentId) !== presentationCount) {
                    push('error', `Component ID ${componentId} uses conflicting global presentation counts.`);
                }
                componentCounts.set(componentId, presentationCount);
                if (utf8Bytes(component.name) > 65535) {
                    push('error', `Component ${component.component_id} name exceeds 65,535 UTF-8 bytes.`);
                }
                if (utf8Bytes(component.description) > 65535) {
                    push('error', `Component ${component.component_id} description exceeds 65,535 UTF-8 bytes.`);
                }

                const enabled = component.criteria.filter((criterion) => number(criterion.enabled) === 1);
                if (number(component.component_type) === 3 && enabled.length) {
                    push('error', `Presentation-only component ${component.component_id} has enabled criteria.`);
                }
                requiredCriteria += enabled.filter((criterion) => number(criterion.behavior) === 0).length;

                const policies = new Set(enabled.map((criterion) => [
                    number(criterion.event_type),
                    number(criterion.progress_mode),
                    number(criterion.behavior),
                    number(criterion.required_count, 1),
                ].join(':')));
                if (policies.size > 1) {
                    push('error', `Alternative criteria for component ${component.component_id} do not share one policy.`);
                }

                enabled.forEach((criterion) => {
                    const event = number(criterion.event_type);
                    const mode = number(criterion.progress_mode);
                    const target = number(criterion.target_id);
                    const target2 = number(criterion.target_id2);
                    const targetValue = number(criterion.target_value);
                    const allowedModes = this.metadata.allowed_progress_modes?.[event];

                    if (Array.isArray(allowedModes) && !allowedModes.map((value) => number(value)).includes(mode)) {
                        push('error', `Component ${component.component_id} uses a progress mode rejected for its event.`);
                    }
                    if (![7, 12, 13].includes(event) && target2 !== 0) {
                        push('error', `Component ${component.component_id} has a secondary target unsupported by its event.`);
                    }
                    if (event === 4 && target === 0) push('error', `Component ${component.component_id} needs an exact task ID.`);
                    if (event === 12 && target === 0) push('error', `Component ${component.component_id} needs a nonzero canonical NPC-name hash.`);
                    if ([1, 10].includes(event) && target !== 0) push('error', `Component ${component.component_id} must use target ID 0 for this event.`);
                    if (mode === 0 && [1, 4, 5, 7, 9, 10, 11, 13].includes(event)) {
                        push('error', `Component ${component.component_id} cannot increment a reconciled absolute or one-time event.`);
                    }
                    if (mode === 3 && [1, 7, 9, 10, 13].includes(event) && targetValue < 1) {
                        push('error', `Component ${component.component_id} needs a positive Boolean threshold.`);
                    }
                });
            });

            if (number(this.editor.enabled) === 1 && requiredCriteria === 0) {
                push('warning', 'This definition has no enabled Required criterion and cannot complete through automatic evaluation.');
            }

            const automaticRewardSequences = new Set();
            this.editor.rewards.forEach((reward) => {
                const sequence = number(reward.sequence);
                const optionId = String(reward.option_id ?? '');
                if (optionId === '') {
                    if (automaticRewardSequences.has(sequence)) {
                        push('error', `Duplicate automatic reward sequence ${sequence}.`);
                    }
                    automaticRewardSequences.add(sequence);
                }
                if (number(reward.amount) < 1) push('error', `Reward sequence ${sequence} needs a positive amount.`);

                const option = optionId === ''
                    ? null
                    : this.editor.reward_set.options.find((candidate) =>
                        String(candidate.option_id) === optionId);
                const published = number(this.editor.enabled) === 1
                    && number(reward.enabled) === 1
                    && (
                        optionId === ''
                        || (
                            this.editor.reward_set.present
                            && number(this.editor.reward_set.enabled) === 1
                            && number(this.editor.reward_set.source_enabled) === 1
                            && number(option?.enabled) === 1
                        )
                    );
                if (!published) return;

                const type = number(reward.reward_type);
                const dataId = number(reward.reward_data_id);
                const amount = number(reward.amount);
                if ([0, 4, 5].includes(type) && dataId < 1) {
                    push('error', `Published reward sequence ${sequence} needs a referenced data ID.`);
                }
                if (type === 0 && amount > 32767) {
                    push('error', `Published item reward sequence ${sequence} exceeds the 32,767 stack limit.`);
                }
                if (type === 1 && (dataId > 1 || amount > 4294967295)) {
                    push('error', `Published XP reward sequence ${sequence} has an invalid mode or exceeds 4,294,967,295 XP.`);
                }
                if (type === 2 && (dataId !== 0 || amount > 2147483647)) {
                    push('error', `Published AA reward sequence ${sequence} requires data 0 and at most 2,147,483,647 points.`);
                }
                if (type === 3 && (dataId !== 0 || amount > 2147483647999)) {
                    push('error', `Published copper reward sequence ${sequence} requires data 0 and at most 2,147,483,647 platinum plus 999 copper.`);
                }
                if (type === 4 && amount > 2147483647) {
                    push('error', `Published alternate-currency reward sequence ${sequence} exceeds 2,147,483,647.`);
                }
                if (type === 5 && (dataId > 2147483647 || amount !== 1)) {
                    push('error', `Published title reward sequence ${sequence} requires a title ID at most 2,147,483,647 and amount 1.`);
                }
            });

            if (
                this.editor.reward_set.present
                && number(this.editor.enabled) === 1
                && number(this.editor.reward_set.enabled) === 1
                && number(this.editor.reward_set.source_enabled) === 1
            ) {
                const options = this.editor.reward_set.options.filter((option) => number(option.enabled) === 1);
                if (!options.some((option) => number(option.common_to_all) === 0)) {
                    push('error', 'An enabled selectable reward set needs at least one enabled non-common choice.');
                }
                options.forEach((option) => {
                    const hasGrant = this.editor.rewards.some((reward) =>
                        number(reward.enabled) === 1
                        && String(reward.option_id) === String(option.option_id));
                    if (!hasGrant) push('error', `Enabled reward option ${option.option_id} has no enabled grant.`);
                });
            }

            if (!this.editor.reward_set.present && this.editor.rewards.some((reward) => String(reward.option_id ?? '') !== '')) {
                push('error', 'Mapped reward grants require a selectable reward set.');
            }

            const optionIds = new Set();
            this.editor.reward_set.options.forEach((option) => {
                const id = number(option.option_id);
                if (optionIds.has(id)) push('error', `Duplicate reward option ID ${id}.`);
                optionIds.add(id);
            });

            const restrictions = new Set();
            this.editor.restrictions.forEach((restriction) => {
                const id = number(restriction.restriction_id);
                if (restrictions.has(id)) push('error', `Duplicate cast restriction ${id}.`);
                restrictions.add(id);
                if (id < 1) push('error', 'Cast restriction IDs must be positive.');
            });

            if (
                this.initialDefinitionVersion !== number(this.editor.version, 0)
                && number(this.editor.reset_on_version_change) === 1
            ) {
                push('warning', 'The version changed with reset enabled. Character completion, progress, and reward ledgers will be cleared when the server rebuilds this definition.');
            }

            return issues;
        },

        issueClass(level) {
            return level === 'error' ? 'alert-error' : 'alert-warning';
        },
    }));
}
