<?php

namespace Tests\Unit\Support\Achievements;

use App\Support\Achievements\AchievementMetadata;
use PHPUnit\Framework\TestCase;

class AchievementMetadataTest extends TestCase
{
    public function test_wire_enum_maps_are_complete_and_stable(): void
    {
        $this->assertSame([0, 1, 2, 3], array_keys(AchievementMetadata::COMPONENT_TYPES));
        $this->assertSame(range(0, 13), array_keys(AchievementMetadata::EVENTS));
        $this->assertSame([0, 1, 2, 3], array_keys(AchievementMetadata::PROGRESS_MODES));
        $this->assertSame(range(0, 5), array_keys(AchievementMetadata::BEHAVIORS));
        $this->assertSame(range(0, 5), array_keys(AchievementMetadata::REWARD_TYPES));

        $this->assertSame(
            [0, 1, 2],
            AchievementMetadata::STATE_BEARING_COMPONENT_TYPES
        );
        $this->assertFalse(
            AchievementMetadata::isStateBearingComponentType(
                AchievementMetadata::COMPONENT_TYPE_PRESENTATION
            )
        );
    }

    public function test_skill_use_type_labels_preserve_the_authoritative_zero_based_ids(): void
    {
        $this->assertSame(range(0, 77), array_keys(AchievementMetadata::SKILL_USE_TYPES));
        $this->assertSame('1H Blunt', AchievementMetadata::SKILL_USE_TYPES[0]);
        $this->assertSame('Dual Wield', AchievementMetadata::SKILL_USE_TYPES[22]);
        $this->assertSame('Offense', AchievementMetadata::SKILL_USE_TYPES[33]);
        $this->assertSame('Specialize Divination', AchievementMetadata::SKILL_USE_TYPES[46]);
        $this->assertSame('Remove Trap', AchievementMetadata::SKILL_USE_TYPES[75]);
        $this->assertSame('Triple Attack', AchievementMetadata::SKILL_USE_TYPES[76]);
        $this->assertSame('2H Piercing', AchievementMetadata::SKILL_USE_TYPES[77]);
    }

    public function test_character_status_maps_match_the_durable_protocol_values(): void
    {
        $this->assertSame([
            0 => 'Claimed / In Flight',
            1 => 'Durably Granted',
            2 => 'Retryable Failure',
        ], AchievementMetadata::CHARACTER_REWARD_STATUSES);

        $this->assertSame([
            0 => 'Pending / In Progress',
            1 => 'Fully Granted',
            2 => 'Retryable Failure',
            3 => 'Ambiguous Delivery',
        ], AchievementMetadata::CHARACTER_SELECTION_STATUSES);

        $this->assertSame([
            0 => 'Pending',
            1 => 'Blocked',
            2 => 'Processing',
        ], AchievementMetadata::CHARACTER_MUTATION_STATUSES);

        $this->assertSame([
            0 => 'Character',
            1 => 'Group',
            2 => 'Raid',
            3 => 'Dynamic Zone',
            4 => 'Shared Task',
        ], AchievementMetadata::MUTATION_TARGET_TYPES);

        $this->assertSame(60, AchievementMetadata::MUTATION_PROCESSING_LEASE_SECONDS);
    }

    public function test_progress_mode_matrix_rejects_increment_for_replayed_or_absolute_events(): void
    {
        $allModes = [
            AchievementMetadata::PROGRESS_INCREMENT,
            AchievementMetadata::PROGRESS_HIGHEST,
            AchievementMetadata::PROGRESS_SET,
            AchievementMetadata::PROGRESS_BOOLEAN,
        ];
        $nonIncrementModes = [
            AchievementMetadata::PROGRESS_HIGHEST,
            AchievementMetadata::PROGRESS_SET,
            AchievementMetadata::PROGRESS_BOOLEAN,
        ];

        foreach ([0, 2, 3, 5, 6, 8, 12] as $eventType) {
            $this->assertSame($allModes, AchievementMetadata::allowedProgressModes($eventType));
        }

        foreach ([1, 4, 7, 9, 10, 11, 13] as $eventType) {
            $this->assertSame(
                $nonIncrementModes,
                AchievementMetadata::allowedProgressModes($eventType)
            );
            $this->assertFalse(
                AchievementMetadata::isProgressModeAllowed(
                    $eventType,
                    AchievementMetadata::PROGRESS_INCREMENT
                )
            );
        }

        $this->assertSame([], AchievementMetadata::allowedProgressModes(255));
        $this->assertFalse(AchievementMetadata::isProgressModeAllowed(255, 0));
    }

    public function test_boolean_threshold_and_target_guidance_cover_every_event(): void
    {
        foreach (range(0, 13) as $eventType) {
            $guidance = AchievementMetadata::targetGuidance($eventType);

            $this->assertSame([
                'target_id_label',
                'target_id_help',
                'target_id2_label',
                'target_id2_help',
                'target_value_label',
                'target_value_help',
                'replay',
            ], array_keys($guidance));
        }

        foreach ([1, 7, 9, 10, 13] as $eventType) {
            $this->assertTrue(
                AchievementMetadata::booleanModeRequiresPositiveTargetValue($eventType)
            );
        }

        foreach ([0, 2, 3, 4, 5, 6, 8, 11, 12] as $eventType) {
            $this->assertFalse(
                AchievementMetadata::booleanModeRequiresPositiveTargetValue($eventType)
            );
        }

        $this->assertStringContainsString(
            '4294967295',
            AchievementMetadata::targetGuidance(AchievementMetadata::EVENT_SKILL_VALUE)[
                'target_id_help'
            ]
        );
        $this->assertSame(4294967295, AchievementMetadata::SKILL_WILDCARD_TARGET_ID);
    }

    public function test_npc_name_canonicalization_matches_the_eqemu_ascii_rules(): void
    {
        $cases = [
            '' => '',
            '1234_--!!' => '',
            '  ORC__Warlord_01!!  ' => 'orc warlord',
            'orc-warlord' => 'orcwarlord',
            '___A___B___' => 'a b',
            "\xC3\x89lite_Orc" => 'lite orc',
            'A.B/C' => 'abc',
        ];

        foreach ($cases as $input => $expected) {
            $this->assertSame($expected, AchievementMetadata::canonicalNpcName($input));
        }
    }

    public function test_npc_name_hash_matches_the_documented_fnv_1a_identity(): void
    {
        $this->assertSame(1660326528, AchievementMetadata::npcNameIdentityHash('orc warlord'));
        $this->assertSame(3826002220, AchievementMetadata::npcNameIdentityHash('a'));
        $this->assertSame(0, AchievementMetadata::npcNameIdentityHash(''));
        $this->assertSame(0, AchievementMetadata::npcNameIdentityHash('1234_--'));

        foreach (['ORC WARLORD', 'orc__warlord', 'Orc Warlord 01!'] as $variant) {
            $this->assertSame(1660326528, AchievementMetadata::hashNpcName($variant));
        }

        $this->assertNotSame(
            AchievementMetadata::npcNameIdentityHash('orc warlord'),
            AchievementMetadata::npcNameIdentityHash('orc-warlord')
        );
    }

    public function test_label_helpers_are_fail_visible_for_unknown_values(): void
    {
        $this->assertSame('NPC Name Kill', AchievementMetadata::eventLabel(12));
        $this->assertSame('Boolean', AchievementMetadata::progressModeLabel(3));
        $this->assertSame('Blocker', AchievementMetadata::behaviorLabel(5));
        $this->assertSame('Title', AchievementMetadata::rewardTypeLabel(5));
        $this->assertSame('Unknown (99)', AchievementMetadata::componentTypeLabel(99));
        $this->assertSame('Unknown (99)', AchievementMetadata::eventLabel(99));
        $this->assertSame('Unknown (99)', AchievementMetadata::characterRewardStatusLabel(99));
        $this->assertSame('Unknown (99)', AchievementMetadata::characterSelectionStatusLabel(99));
        $this->assertSame('Unknown (99)', AchievementMetadata::characterMutationStatusLabel(99));
    }
}
