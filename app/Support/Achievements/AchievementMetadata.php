<?php

namespace App\Support\Achievements;

final class AchievementMetadata
{
    public const COMPONENT_TYPE_0 = 0;

    public const COMPONENT_TYPE_1 = 1;

    public const COMPONENT_TYPE_2 = 2;

    public const COMPONENT_TYPE_PRESENTATION = 3;

    /**
     * Component types are RoF2 wire buckets, not criterion behaviors.
     */
    public const COMPONENT_TYPES = [
        self::COMPONENT_TYPE_0 => 'Type 0 (state-bearing)',
        self::COMPONENT_TYPE_1 => 'Type 1 (state-bearing)',
        self::COMPONENT_TYPE_2 => 'Type 2 (state-bearing)',
        self::COMPONENT_TYPE_PRESENTATION => 'Type 3 (presentation only)',
    ];

    public const STATE_BEARING_COMPONENT_TYPES = [
        self::COMPONENT_TYPE_0,
        self::COMPONENT_TYPE_1,
        self::COMPONENT_TYPE_2,
    ];

    public const EVENT_MANUAL = 0;

    public const EVENT_LEVEL = 1;

    public const EVENT_NPC_KILL = 2;

    public const EVENT_NPC_RACE_KILL = 3;

    public const EVENT_TASK_COMPLETE = 4;

    public const EVENT_ZONE_ENTER = 5;

    public const EVENT_LOOT_ITEM = 6;

    public const EVENT_OWN_ITEM = 7;

    public const EVENT_TRADESKILL_SUCCESS = 8;

    public const EVENT_SKILL_VALUE = 9;

    public const EVENT_ALTERNATE_ADVANCEMENT = 10;

    public const EVENT_ACHIEVEMENT_COMPLETE = 11;

    public const EVENT_NPC_NAME_KILL = 12;

    public const EVENT_SKILL_CAP = 13;

    public const EVENTS = [
        self::EVENT_MANUAL => 'Manual',
        self::EVENT_LEVEL => 'Level',
        self::EVENT_NPC_KILL => 'NPC Type Kill',
        self::EVENT_NPC_RACE_KILL => 'NPC Race Kill',
        self::EVENT_TASK_COMPLETE => 'Task Complete',
        self::EVENT_ZONE_ENTER => 'Zone Enter',
        self::EVENT_LOOT_ITEM => 'Loot Item',
        self::EVENT_OWN_ITEM => 'Own Item',
        self::EVENT_TRADESKILL_SUCCESS => 'Tradeskill Success',
        self::EVENT_SKILL_VALUE => 'Skill Value',
        self::EVENT_ALTERNATE_ADVANCEMENT => 'Alternate Advancement',
        self::EVENT_ACHIEVEMENT_COMPLETE => 'Achievement Complete',
        self::EVENT_NPC_NAME_KILL => 'NPC Name Kill',
        self::EVENT_SKILL_CAP => 'Skill Cap',
    ];

    /**
     * EQEmu SkillUseTypes accepted by Skill Value and Skill Cap criteria.
     *
     * @see https://docs.eqemu.dev/server/player/skills/
     */
    public const SKILL_USE_TYPES = [
        0 => '1H Blunt',
        1 => '1H Slashing',
        2 => '2H Blunt',
        3 => '2H Slashing',
        4 => 'Abjuration',
        5 => 'Alteration',
        6 => 'Apply Poison',
        7 => 'Archery',
        8 => 'Backstab',
        9 => 'Bind Wound',
        10 => 'Bash',
        11 => 'Block',
        12 => 'Brass Instruments',
        13 => 'Channeling',
        14 => 'Conjuration',
        15 => 'Defense',
        16 => 'Disarm',
        17 => 'Disarm Traps',
        18 => 'Divination',
        19 => 'Dodge',
        20 => 'Double Attack',
        21 => 'Dragon Punch',
        22 => 'Dual Wield',
        23 => 'Eagle Strike',
        24 => 'Evocation',
        25 => 'Feign Death',
        26 => 'Flying Kick',
        27 => 'Forage',
        28 => 'Hand to Hand',
        29 => 'Hide',
        30 => 'Kick',
        31 => 'Meditate',
        32 => 'Mend',
        33 => 'Offense',
        34 => 'Parry',
        35 => 'Pick Lock',
        36 => '1H Piercing',
        37 => 'Riposte',
        38 => 'Round Kick',
        39 => 'Safe Fall',
        40 => 'Sense Heading',
        41 => 'Singing',
        42 => 'Sneak',
        43 => 'Specialize Abjure',
        44 => 'Specialize Alteration',
        45 => 'Specialize Conjuration',
        46 => 'Specialize Divination',
        47 => 'Specialize Evocation',
        48 => 'Pick Pockets',
        49 => 'Stringed Instruments',
        50 => 'Swimming',
        51 => 'Throwing',
        52 => 'Tiger Claw',
        53 => 'Tracking',
        54 => 'Wind Instruments',
        55 => 'Fishing',
        56 => 'Make Poison',
        57 => 'Tinkering',
        58 => 'Research',
        59 => 'Alchemy',
        60 => 'Baking',
        61 => 'Tailoring',
        62 => 'Sense Traps',
        63 => 'Blacksmithing',
        64 => 'Fletching',
        65 => 'Brewing',
        66 => 'Alcohol Tolerance',
        67 => 'Begging',
        68 => 'Jewelry Making',
        69 => 'Pottery',
        70 => 'Percussion Instruments',
        71 => 'Intimidation',
        72 => 'Berserking',
        73 => 'Taunt',
        74 => 'Frenzy',
        75 => 'Remove Trap',
        76 => 'Triple Attack',
        77 => '2H Piercing',
    ];

    public const PROGRESS_INCREMENT = 0;

    public const PROGRESS_HIGHEST = 1;

    public const PROGRESS_SET = 2;

    public const PROGRESS_BOOLEAN = 3;

    public const PROGRESS_MODES = [
        self::PROGRESS_INCREMENT => 'Increment',
        self::PROGRESS_HIGHEST => 'Highest',
        self::PROGRESS_SET => 'Set',
        self::PROGRESS_BOOLEAN => 'Boolean',
    ];

    /**
     * Mirrors the fail-closed validation in AchievementManager.
     */
    public const ALLOWED_PROGRESS_MODES = [
        self::EVENT_MANUAL => [
            self::PROGRESS_INCREMENT,
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_LEVEL => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_NPC_KILL => [
            self::PROGRESS_INCREMENT,
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_NPC_RACE_KILL => [
            self::PROGRESS_INCREMENT,
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_TASK_COMPLETE => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_ZONE_ENTER => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_LOOT_ITEM => [
            self::PROGRESS_INCREMENT,
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_OWN_ITEM => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_TRADESKILL_SUCCESS => [
            self::PROGRESS_INCREMENT,
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_SKILL_VALUE => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_ALTERNATE_ADVANCEMENT => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_ACHIEVEMENT_COMPLETE => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_NPC_NAME_KILL => [
            self::PROGRESS_INCREMENT,
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
        self::EVENT_SKILL_CAP => [
            self::PROGRESS_HIGHEST,
            self::PROGRESS_SET,
            self::PROGRESS_BOOLEAN,
        ],
    ];

    public const BEHAVIOR_REQUIRED = 0;

    public const BEHAVIOR_OPTIONAL = 1;

    public const BEHAVIOR_UNLOCK = 2;

    public const BEHAVIOR_VISIBILITY = 3;

    public const BEHAVIOR_DISPLAY_ONLY = 4;

    public const BEHAVIOR_BLOCKER = 5;

    public const BEHAVIORS = [
        self::BEHAVIOR_REQUIRED => 'Required',
        self::BEHAVIOR_OPTIONAL => 'Optional',
        self::BEHAVIOR_UNLOCK => 'Unlock',
        self::BEHAVIOR_VISIBILITY => 'Visibility',
        self::BEHAVIOR_DISPLAY_ONLY => 'Display Only',
        self::BEHAVIOR_BLOCKER => 'Blocker',
    ];

    public const REWARD_ITEM = 0;

    public const REWARD_EXPERIENCE = 1;

    public const REWARD_ALTERNATE_ADVANCEMENT = 2;

    public const REWARD_COPPER = 3;

    public const REWARD_ALTERNATE_CURRENCY = 4;

    public const REWARD_TITLE = 5;

    public const REWARD_TYPES = [
        self::REWARD_ITEM => 'Item',
        self::REWARD_EXPERIENCE => 'Experience',
        self::REWARD_ALTERNATE_ADVANCEMENT => 'Alternate Advancement',
        self::REWARD_COPPER => 'Copper',
        self::REWARD_ALTERNATE_CURRENCY => 'Alternate Currency',
        self::REWARD_TITLE => 'Title',
    ];

    public const ACHIEVEMENT_STATUS_COMPLETED = 0;

    public const ACHIEVEMENT_STATUS_OPEN = 1;

    public const ACHIEVEMENT_STATUS_LOCKED = 2;

    public const ACHIEVEMENT_STATUS_HIDDEN = 3;

    public const ACHIEVEMENT_STATUSES = [
        self::ACHIEVEMENT_STATUS_COMPLETED => 'Completed',
        self::ACHIEVEMENT_STATUS_OPEN => 'Open',
        self::ACHIEVEMENT_STATUS_LOCKED => 'Locked',
        self::ACHIEVEMENT_STATUS_HIDDEN => 'Hidden',
    ];

    public const CHARACTER_REWARD_STATUS_IN_FLIGHT = 0;

    public const CHARACTER_REWARD_STATUS_GRANTED = 1;

    public const CHARACTER_REWARD_STATUS_RETRYABLE_FAILURE = 2;

    public const CHARACTER_REWARD_STATUSES = [
        self::CHARACTER_REWARD_STATUS_IN_FLIGHT => 'Claimed / In Flight',
        self::CHARACTER_REWARD_STATUS_GRANTED => 'Durably Granted',
        self::CHARACTER_REWARD_STATUS_RETRYABLE_FAILURE => 'Retryable Failure',
    ];

    public const CHARACTER_SELECTION_STATUS_PENDING = 0;

    public const CHARACTER_SELECTION_STATUS_GRANTED = 1;

    public const CHARACTER_SELECTION_STATUS_RETRYABLE_FAILURE = 2;

    public const CHARACTER_SELECTION_STATUS_AMBIGUOUS = 3;

    public const CHARACTER_SELECTION_STATUSES = [
        self::CHARACTER_SELECTION_STATUS_PENDING => 'Pending / In Progress',
        self::CHARACTER_SELECTION_STATUS_GRANTED => 'Fully Granted',
        self::CHARACTER_SELECTION_STATUS_RETRYABLE_FAILURE => 'Retryable Failure',
        self::CHARACTER_SELECTION_STATUS_AMBIGUOUS => 'Ambiguous Delivery',
    ];

    public const UPDATE_TARGET_CHARACTER = 0;

    public const UPDATE_TARGET_GROUP = 1;

    public const UPDATE_TARGET_RAID = 2;

    public const UPDATE_TARGET_DYNAMIC_ZONE = 3;

    public const UPDATE_TARGET_SHARED_TASK = 4;

    public const UPDATE_TARGET_TYPES = [
        self::UPDATE_TARGET_CHARACTER => 'Character',
        self::UPDATE_TARGET_GROUP => 'Group',
        self::UPDATE_TARGET_RAID => 'Raid',
        self::UPDATE_TARGET_DYNAMIC_ZONE => 'Dynamic Zone',
        self::UPDATE_TARGET_SHARED_TASK => 'Shared Task',
    ];

    public const UPDATE_OPERATION_ADVANCE = 0;

    public const UPDATE_OPERATION_COMPLETE = 1;

    public const UPDATE_OPERATIONS = [
        self::UPDATE_OPERATION_ADVANCE => 'Advance',
        self::UPDATE_OPERATION_COMPLETE => 'Complete',
    ];

    public const CHARACTER_UPDATE_STATUS_PENDING = 0;

    public const CHARACTER_UPDATE_STATUS_BLOCKED = 1;

    public const CHARACTER_UPDATE_STATUS_PROCESSING = 2;

    public const CHARACTER_UPDATE_STATUSES = [
        self::CHARACTER_UPDATE_STATUS_PENDING => 'Pending',
        self::CHARACTER_UPDATE_STATUS_BLOCKED => 'Blocked',
        self::CHARACTER_UPDATE_STATUS_PROCESSING => 'Processing',
    ];

    public const SKILL_WILDCARD_TARGET_ID = 4294967295;

    public const UPDATE_PROCESSING_LEASE_SECONDS = 60;

    /**
     * UI-facing target semantics from achievement_authoring.md.
     */
    public const TARGET_GUIDANCE = [
        self::EVENT_MANUAL => [
            'target_id_label' => 'Target ID',
            'target_id_help' => 'Normally 0. Manual criteria have no engine event.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Target Value',
            'target_value_help' => 'Normally 0; direct progress calls supply the value.',
            'replay' => 'No engine replay.',
        ],
        self::EVENT_LEVEL => [
            'target_id_label' => 'Target ID',
            'target_id_help' => 'Must be 0.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Level',
            'target_value_help' => 'Positive level threshold for Boolean mode.',
            'replay' => 'Current level is reconciled on login and zone load.',
        ],
        self::EVENT_NPC_KILL => [
            'target_id_label' => 'NPC Type ID',
            'target_id_help' => 'npc_types.id, or 0 to match any NPC type.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Event Value',
            'target_value_help' => 'A credited kill observes 1; normally use 0 or 1.',
            'replay' => 'No historical replay.',
        ],
        self::EVENT_NPC_RACE_KILL => [
            'target_id_label' => 'NPC Race ID',
            'target_id_help' => 'npc_types.race, or 0 to match any race.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Event Value',
            'target_value_help' => 'A credited kill observes 1; normally use 0 or 1.',
            'replay' => 'No historical replay.',
        ],
        self::EVENT_TASK_COMPLETE => [
            'target_id_label' => 'Task ID',
            'target_id_help' => 'Exact nonzero tasks.id. Wildcards are rejected.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Event Value',
            'target_value_help' => 'Completion observes 1; normally use 0 or 1.',
            'replay' => 'Replayed from completed-task history.',
        ],
        self::EVENT_ZONE_ENTER => [
            'target_id_label' => 'Zone ID',
            'target_id_help' => 'Zone ID, or 0 to match any destination zone.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Event Value',
            'target_value_help' => 'Zone entry observes 1; normally use 0 or 1.',
            'replay' => 'Only the current zone is reconciled. Increment mode is rejected; use Highest, Set, or Boolean.',
        ],
        self::EVENT_LOOT_ITEM => [
            'target_id_label' => 'Item ID',
            'target_id_help' => 'items.id, or 0 to match any item looted from an NPC corpse.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Transfer Quantity',
            'target_value_help' => 'Qualifying event value is the successfully transferred quantity.',
            'replay' => 'No historical replay.',
        ],
        self::EVENT_OWN_ITEM => [
            'target_id_label' => 'Item ID',
            'target_id_help' => 'items.id, or 0 for the greatest quantity held of any one item ID.',
            'target_id2_label' => 'Required Class',
            'target_id2_help' => 'Optional EQ class ID; 0 is class-neutral.',
            'target_value_label' => 'Minimum Owned Count',
            'target_value_help' => 'Required to be positive when Boolean mode is used.',
            'replay' => 'Authoritative persisted ownership is reconciled.',
        ],
        self::EVENT_TRADESKILL_SUCCESS => [
            'target_id_label' => 'Recipe ID',
            'target_id_help' => 'tradeskill_recipe.id, or 0 to match any successful recipe.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Event Value',
            'target_value_help' => 'A successful combine observes 1; normally use 0 or 1.',
            'replay' => 'No historical replay.',
        ],
        self::EVENT_SKILL_VALUE => [
            'target_id_label' => 'Skill ID',
            'target_id_help' => 'Exact skill ID. Use 4294967295 for any skill; 0 is 1H Blunt.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Skill Value',
            'target_value_help' => 'Required to be positive when Boolean mode is used.',
            'replay' => 'Persisted raw skill value is reconciled.',
        ],
        self::EVENT_ALTERNATE_ADVANCEMENT => [
            'target_id_label' => 'Target ID',
            'target_id_help' => 'Must be 0.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Spent AA',
            'target_value_help' => 'Purchased-rank cost plus durable expended AA; positive for Boolean mode.',
            'replay' => 'Spent AA is reconciled.',
        ],
        self::EVENT_ACHIEVEMENT_COMPLETE => [
            'target_id_label' => 'Prerequisite Achievement ID',
            'target_id_help' => 'achievements.id, or 0 to match any completed achievement.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Must be 0.',
            'target_value_label' => 'Minimum Event Value',
            'target_value_help' => 'Completion observes 1; normally use 0 or 1.',
            'replay' => 'Replayed from durable achievement completion state.',
        ],
        self::EVENT_NPC_NAME_KILL => [
            'target_id_label' => 'Canonical NPC Name Hash',
            'target_id_help' => 'Required nonzero 32-bit FNV-1a identity; use npcNameIdentityHash().',
            'target_id2_label' => 'Zone ID',
            'target_id2_help' => 'Zone ID, or 0 to match the name in any zone.',
            'target_value_label' => 'Minimum Event Value',
            'target_value_help' => 'A credited kill observes 1; normally use 0 or 1.',
            'replay' => 'No historical replay; audit hash collisions within the target zone.',
        ],
        self::EVENT_SKILL_CAP => [
            'target_id_label' => 'Skill ID',
            'target_id_help' => 'Exact skill ID; 0 validly means 1H Blunt.',
            'target_id2_label' => 'Required Class',
            'target_id2_help' => 'Required EQ class ID from Warrior through Berserker.',
            'target_value_label' => 'Milestone Level',
            'target_value_help' => 'Required level from 1 through 255 used to resolve the DB-backed skill cap.',
            'replay' => 'Class, level, and DB-backed cap attainment are reconciled.',
        ],
    ];

    private const FNV_1A_OFFSET_BASIS = 2166136261;

    private const FNV_1A_PRIME = 16777619;

    public static function componentTypeLabel(int $componentType): string
    {
        return self::COMPONENT_TYPES[$componentType] ?? "Unknown ({$componentType})";
    }

    public static function eventLabel(int $eventType): string
    {
        return self::EVENTS[$eventType] ?? "Unknown ({$eventType})";
    }

    public static function progressModeLabel(int $progressMode): string
    {
        return self::PROGRESS_MODES[$progressMode] ?? "Unknown ({$progressMode})";
    }

    public static function behaviorLabel(int $behavior): string
    {
        return self::BEHAVIORS[$behavior] ?? "Unknown ({$behavior})";
    }

    public static function rewardTypeLabel(int $rewardType): string
    {
        return self::REWARD_TYPES[$rewardType] ?? "Unknown ({$rewardType})";
    }

    public static function achievementStatusLabel(int $status): string
    {
        return self::ACHIEVEMENT_STATUSES[$status] ?? "Unknown ({$status})";
    }

    public static function characterRewardStatusLabel(int $status): string
    {
        return self::CHARACTER_REWARD_STATUSES[$status] ?? "Unknown ({$status})";
    }

    public static function characterSelectionStatusLabel(int $status): string
    {
        return self::CHARACTER_SELECTION_STATUSES[$status] ?? "Unknown ({$status})";
    }

    public static function updateTargetTypeLabel(int $targetType): string
    {
        return self::UPDATE_TARGET_TYPES[$targetType] ?? "Unknown ({$targetType})";
    }

    public static function updateOperationLabel(int $operation): string
    {
        return self::UPDATE_OPERATIONS[$operation] ?? "Unknown ({$operation})";
    }

    public static function characterUpdateStatusLabel(int $status): string
    {
        return self::CHARACTER_UPDATE_STATUSES[$status] ?? "Unknown ({$status})";
    }

    public static function targetGuidance(int $eventType): array
    {
        return self::TARGET_GUIDANCE[$eventType] ?? [
            'target_id_label' => 'Target ID',
            'target_id_help' => 'Unknown event type.',
            'target_id2_label' => 'Secondary Target',
            'target_id2_help' => 'Unknown event type.',
            'target_value_label' => 'Target Value',
            'target_value_help' => 'Unknown event type.',
            'replay' => 'Unknown event type.',
        ];
    }

    public static function allowedProgressModes(int $eventType): array
    {
        return self::ALLOWED_PROGRESS_MODES[$eventType] ?? [];
    }

    public static function isProgressModeAllowed(int $eventType, int $progressMode): bool
    {
        return in_array($progressMode, self::allowedProgressModes($eventType), true);
    }

    public static function isStateBearingComponentType(int $componentType): bool
    {
        return in_array($componentType, self::STATE_BEARING_COMPONENT_TYPES, true);
    }

    public static function booleanModeRequiresPositiveTargetValue(int $eventType): bool
    {
        return in_array($eventType, [
            self::EVENT_LEVEL,
            self::EVENT_OWN_ITEM,
            self::EVENT_SKILL_VALUE,
            self::EVENT_SKILL_CAP,
            self::EVENT_ALTERNATE_ADVANCEMENT,
        ], true);
    }

    /**
     * Normalize an NPC name exactly as EQEmu does before hashing it.
     *
     * Only ASCII letters survive. ASCII uppercase is folded to lowercase,
     * spaces and underscores collapse to one interior space, and digits,
     * punctuation, spawn suffixes, and non-ASCII bytes are discarded.
     */
    public static function canonicalNpcName(string $name): string
    {
        $canonical = '';
        $hasLetter = false;
        $separatorPending = false;

        for ($i = 0, $length = strlen($name); $i < $length; $i++) {
            $byte = ord($name[$i]);

            if ($byte === 32 || $byte === 95) {
                $separatorPending = $hasLetter;

                continue;
            }

            if ($byte >= 65 && $byte <= 90) {
                $byte += 32;
            }

            if ($byte < 97 || $byte > 122) {
                continue;
            }

            if ($separatorPending) {
                $canonical .= ' ';
                $separatorPending = false;
            }

            $canonical .= chr($byte);
            $hasLetter = true;
        }

        return $canonical;
    }

    /**
     * Return EQEmu's unsigned 32-bit canonical NPC-name FNV-1a identity.
     */
    public static function npcNameIdentityHash(string $name): int
    {
        $canonical = self::canonicalNpcName($name);
        if ($canonical === '') {
            return 0;
        }

        $hash = self::FNV_1A_OFFSET_BASIS;
        for ($i = 0, $length = strlen($canonical); $i < $length; $i++) {
            $hash ^= ord($canonical[$i]);
            $hash = ($hash * self::FNV_1A_PRIME) & 0xFFFFFFFF;
        }

        return $hash ?: 0;
    }

    public static function hashNpcName(string $name): int
    {
        return self::npcNameIdentityHash($name);
    }
}
