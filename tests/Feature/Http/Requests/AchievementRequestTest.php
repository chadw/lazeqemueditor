<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\AchievementRequest;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Tests\TestCase;

class AchievementRequestTest extends TestCase
{
    public function test_valid_definition_passes_and_form_values_are_normalized(): void
    {
        $payload = $this->validPayload();
        $payload['description'] = null;
        $payload['icon_id'] = '';
        $payload['points'] = '';
        $payload['has_reward'] = '';
        $payload['client_flag'] = '';
        $payload['version'] = '';
        $payload['reset_on_version_change'] = 'on';
        $payload['enabled'] = 'true';
        $payload['components'][0]['criteria'][0]['target_id'] = '';
        $payload['components'][0]['criteria'][0]['target_id2'] = '';
        $payload['components'][0]['criteria'][0]['target_value'] = '';
        $payload['components'][0]['criteria'][0]['required_count'] = '';
        $payload['components'][0]['criteria'][0]['enabled'] = 'yes';
        $payload['reward_set'] = ['present' => '0'];

        [$request, $validator] = $this->validatorFor($payload);

        $this->assertValidatorPasses($validator);
        $this->assertSame('', $request->input('description'));
        $this->assertSame(0, $request->input('icon_id'));
        $this->assertSame(0, $request->input('version'));
        $this->assertSame(1, $request->input('reset_on_version_change'));
        $this->assertSame(1, $request->input('enabled'));
        $this->assertSame(
            1,
            $request->input('components.0.criteria.0.required_count')
        );
        $this->assertNull($request->input('reward_set'));
    }

    public function test_enabled_definition_requires_unique_category_associations(): void
    {
        $missing = $this->validPayload();
        $missing['associations'] = [];
        [, $missingValidator] = $this->validatorFor($missing);
        $this->assertValidationError($missingValidator, 'associations');

        $duplicate = $this->validPayload();
        $duplicate['associations'][] = $duplicate['associations'][0];
        [, $duplicateValidator] = $this->validatorFor($duplicate);
        $this->assertValidationError(
            $duplicateValidator,
            'associations.1.category_id'
        );
    }

    public function test_stable_achievement_id_cannot_change_on_update(): void
    {
        [, $validator] = $this->validatorFor(
            $this->validPayload(),
            ['achievement' => 101]
        );

        $this->assertValidationError($validator, 'id');
    }

    public function test_component_identity_and_global_presentation_count_are_guarded(): void
    {
        $duplicate = $this->validPayload();
        $duplicate['components'][] = $duplicate['components'][0];
        [, $duplicateValidator] = $this->validatorFor($duplicate);
        $this->assertValidationError(
            $duplicateValidator,
            'components.1.component_id'
        );

        $countMismatch = $this->validPayload();
        $second = $countMismatch['components'][0];
        $second['component_type'] = 2;
        $second['presentation_count'] = 2;
        $second['criteria'] = [];
        $countMismatch['components'][] = $second;
        [, $countValidator] = $this->validatorFor($countMismatch);
        $this->assertValidationError(
            $countValidator,
            'components.1.presentation_count'
        );
    }

    public function test_presentation_only_components_cannot_have_enabled_criteria(): void
    {
        $payload = $this->validPayload();
        $payload['components'][0]['component_type'] = 3;

        [, $validator] = $this->validatorFor($payload);

        $this->assertValidationError(
            $validator,
            'components.0.criteria.0.enabled'
        );
    }

    public function test_alternative_criteria_require_unique_targets_and_one_policy(): void
    {
        $duplicateIdentity = $this->validPayload();
        $duplicateIdentity['components'][0]['criteria'][] =
            $duplicateIdentity['components'][0]['criteria'][0];
        [, $identityValidator] = $this->validatorFor($duplicateIdentity);
        $this->assertValidationError(
            $identityValidator,
            'components.0.criteria.1.target_id'
        );

        $policyMismatch = $this->validPayload();
        $alternative = $policyMismatch['components'][0]['criteria'][0];
        $alternative['target_id'] = 1;
        $alternative['progress_mode'] = 1;
        $policyMismatch['components'][0]['criteria'][] = $alternative;
        [, $policyValidator] = $this->validatorFor($policyMismatch);
        $this->assertValidationError(
            $policyValidator,
            'components.0.criteria.1'
        );
    }

    public function test_replayed_and_absolute_events_reject_increment_mode(): void
    {
        $cases = [
            [1, 0, 0, 60],
            [4, 25, 0, 0],
            [5, 0, 0, 1],
            [7, 1001, 0, 1],
            [9, 0, 0, 200],
            [10, 0, 0, 50],
            [11, 99, 0, 0],
            [13, 0, 1, 60],
        ];

        foreach ($cases as [$eventType, $targetId, $targetId2, $targetValue]) {
            $payload = $this->payloadWithCriterion(
                $eventType,
                0,
                $targetId,
                $targetId2,
                $targetValue
            );
            [, $validator] = $this->validatorFor($payload);

            $this->assertValidationError(
                $validator,
                'components.0.criteria.0.progress_mode'
            );
        }
    }

    public function test_event_specific_target_guards_reject_unsafe_content(): void
    {
        $cases = [
            [12, 0, 0, 1, 'components.0.criteria.0.target_id'],
            [4, 0, 0, 1, 'components.0.criteria.0.target_id'],
            [2, 10, 1, 1, 'components.0.criteria.0.target_id2'],
            [1, 1, 0, 60, 'components.0.criteria.0.target_id'],
            [7, 1001, 17, 1, 'components.0.criteria.0.target_id2'],
            [9, 78, 0, 200, 'components.0.criteria.0.target_id'],
            [13, 0, 0, 60, 'components.0.criteria.0'],
            [13, 0, 1, 256, 'components.0.criteria.0'],
        ];

        foreach ($cases as [$eventType, $targetId, $targetId2, $targetValue, $errorKey]) {
            $payload = $this->payloadWithCriterion(
                $eventType,
                1,
                $targetId,
                $targetId2,
                $targetValue
            );
            [, $validator] = $this->validatorFor($payload);

            $this->assertValidationError($validator, $errorKey);
        }
    }

    public function test_skill_value_wildcard_is_valid_and_boolean_absolute_facts_need_a_threshold(): void
    {
        $wildcard = $this->payloadWithCriterion(9, 1, 4294967295, 0, 200);
        [, $wildcardValidator] = $this->validatorFor($wildcard);
        $this->assertValidatorPasses($wildcardValidator);

        $booleanWithoutThreshold = $this->payloadWithCriterion(1, 3, 0, 0, 0);
        [, $booleanValidator] = $this->validatorFor($booleanWithoutThreshold);
        $this->assertValidationError(
            $booleanValidator,
            'components.0.criteria.0.target_value'
        );
    }

    public function test_completion_affecting_class_criteria_must_agree_on_one_class(): void
    {
        $payload = $this->payloadWithCriterion(7, 1, 1001, 1, 1);
        $second = $payload['components'][0];
        $second['component_id'] = 501;
        $second['criteria'][0]['target_id'] = 1002;
        $second['criteria'][0]['target_id2'] = 2;
        $payload['components'][] = $second;

        [, $validator] = $this->validatorFor($payload);

        $this->assertValidationError($validator, 'components');
    }

    public function test_valid_selectable_reward_set_passes(): void
    {
        [, $validator] = $this->validatorFor($this->validRewardPayload());

        $this->assertValidatorPasses($validator);
    }

    public function test_version_zero_and_full_unsigned_reward_amount_are_preserved_in_a_draft(): void
    {
        $payload = $this->validRewardPayload();
        $payload['version'] = 0;
        $payload['enabled'] = 0;
        $payload['rewards'][0]['amount'] = '18446744073709551615';

        [$request, $validator] = $this->validatorFor($payload);

        $this->assertValidatorPasses($validator);
        $this->assertSame(0, $request->input('version'));
        $this->assertSame(
            '18446744073709551615',
            $request->input('rewards.0.amount')
        );
    }

    public function test_reward_amount_above_unsigned_bigint_is_rejected(): void
    {
        $payload = $this->validRewardPayload();
        $payload['rewards'][0]['amount'] = '18446744073709551616';

        [, $validator] = $this->validatorFor($payload);

        $this->assertValidationError($validator, 'rewards.0.amount');
    }

    public function test_mysql_text_fields_enforce_the_utf8_byte_limit(): void
    {
        $boundary = str_repeat('é', 32_767).'a';
        $valid = $this->validPayload();
        $valid['description'] = $boundary;
        $valid['components'][0]['name'] = $boundary;
        $valid['components'][0]['description'] = $boundary;
        [, $validValidator] = $this->validatorFor($valid);
        $this->assertValidatorPasses($validValidator);

        foreach (['description', 'name', 'component_description'] as $field) {
            $payload = $this->validPayload();
            $oversized = str_repeat('é', 32_768);
            $key = 'description';
            if ($field === 'name') {
                $payload['components'][0]['name'] = $oversized;
                $key = 'components.0.name';
            } elseif ($field === 'component_description') {
                $payload['components'][0]['description'] = $oversized;
                $key = 'components.0.description';
            } else {
                $payload['description'] = $oversized;
            }

            [, $validator] = $this->validatorFor($payload);
            $this->assertValidationError($validator, $key);
        }
    }

    public function test_published_rewards_enforce_delivery_type_bounds_and_semantics(): void
    {
        $validCases = [
            [0, 1001, '32767'],
            [1, 1, '4294967295'],
            [2, 0, '2147483647'],
            [3, 0, '2147483647999'],
            [4, 100, '2147483647'],
            [5, 100, '1'],
        ];
        foreach ($validCases as [$type, $dataId, $amount]) {
            $payload = $this->validRewardPayload();
            $payload['rewards'][0]['reward_type'] = $type;
            $payload['rewards'][0]['reward_data_id'] = $dataId;
            $payload['rewards'][0]['amount'] = $amount;
            [, $validator] = $this->validatorFor($payload);
            $this->assertValidatorPasses($validator);
        }

        $invalidCases = [
            [0, 1001, '32768', 'rewards.0.amount'],
            [1, 0, '4294967296', 'rewards.0.amount'],
            [1, 2, '1', 'rewards.0.reward_data_id'],
            [2, 0, '2147483648', 'rewards.0.amount'],
            [2, 1, '1', 'rewards.0.reward_data_id'],
            [3, 0, '2147483648000', 'rewards.0.amount'],
            [3, 1, '1', 'rewards.0.reward_data_id'],
            [4, 100, '2147483648', 'rewards.0.amount'],
            [5, 2147483648, '1', 'rewards.0.reward_data_id'],
            [5, 100, '2', 'rewards.0.amount'],
        ];
        foreach ($invalidCases as [$type, $dataId, $amount, $key]) {
            $payload = $this->validRewardPayload();
            $payload['rewards'][0]['reward_type'] = $type;
            $payload['rewards'][0]['reward_data_id'] = $dataId;
            $payload['rewards'][0]['amount'] = $amount;
            [, $validator] = $this->validatorFor($payload);
            $this->assertValidationError($validator, $key);
        }

        $draft = $this->validRewardPayload();
        $draft['enabled'] = 0;
        $draft['rewards'][0]['reward_type'] = 5;
        $draft['rewards'][0]['reward_data_id'] = 2147483648;
        $draft['rewards'][0]['amount'] = '2';
        [, $draftValidator] = $this->validatorFor($draft);
        $this->assertValidatorPasses($draftValidator);
    }

    public function test_disabled_selectable_source_can_stage_incomplete_options(): void
    {
        $payload = $this->validRewardPayload();
        $payload['reward_set']['source_enabled'] = 0;
        $payload['rewards'] = [];

        [, $validator] = $this->validatorFor($payload);

        $this->assertValidatorPasses($validator);
    }

    public function test_disabled_definition_can_stage_incomplete_selectable_content(): void
    {
        $payload = $this->validRewardPayload();
        $payload['enabled'] = 0;
        $payload['rewards'] = [];

        [, $validator] = $this->validatorFor($payload);

        $this->assertValidatorPasses($validator);
    }

    public function test_reward_delivery_and_option_mapping_guards_fail_closed(): void
    {
        $missingDataId = $this->validRewardPayload();
        $missingDataId['rewards'][0]['reward_data_id'] = 0;
        [, $dataIdValidator] = $this->validatorFor($missingDataId);
        $this->assertValidationError(
            $dataIdValidator,
            'rewards.0.reward_data_id'
        );

        $unknownOption = $this->validRewardPayload();
        $unknownOption['rewards'][0]['option_id'] = 99;
        [, $mappingValidator] = $this->validatorFor($unknownOption);
        $this->assertValidationError(
            $mappingValidator,
            'rewards.0.option_id'
        );

        $commonOnly = $this->validRewardPayload();
        $commonOnly['reward_set']['options'][0]['common_to_all'] = 1;
        [, $commonValidator] = $this->validatorFor($commonOnly);
        $this->assertValidationError(
            $commonValidator,
            'reward_set.options'
        );

        $emptyOption = $this->validRewardPayload();
        $emptyOption['rewards'] = [];
        [, $emptyValidator] = $this->validatorFor($emptyOption);
        $this->assertValidationError($emptyValidator, 'reward_set.options.0');

        $badExperienceMode = $this->validRewardPayload();
        $badExperienceMode['rewards'][0]['reward_type'] = 1;
        $badExperienceMode['rewards'][0]['reward_data_id'] = 2;
        [, $experienceValidator] = $this->validatorFor($badExperienceMode);
        $this->assertValidationError(
            $experienceValidator,
            'rewards.0.reward_data_id'
        );
    }

    public function test_reward_ids_sequences_and_restrictions_are_unique(): void
    {
        $duplicateReward = $this->validRewardPayload();
        $duplicateReward['rewards'][0]['reward_id'] = 900;
        $duplicateReward['rewards'][] = $duplicateReward['rewards'][0];
        [, $rewardValidator] = $this->validatorFor($duplicateReward);
        $this->assertValidationError($rewardValidator, 'rewards.1.reward_id');

        $duplicateAutomaticSequence = $this->validPayload();
        $duplicateAutomaticSequence['rewards'] = [
            [
                'reward_id' => null,
                'sequence' => 7,
                'reward_type' => 2,
                'reward_data_id' => 0,
                'amount' => 1,
                'description' => 'First',
                'enabled' => 1,
                'option_id' => null,
            ],
            [
                'reward_id' => null,
                'sequence' => 7,
                'reward_type' => 3,
                'reward_data_id' => 0,
                'amount' => 1,
                'description' => 'Second',
                'enabled' => 1,
                'option_id' => null,
            ],
        ];
        [, $sequenceValidator] = $this->validatorFor($duplicateAutomaticSequence);
        $this->assertValidationError($sequenceValidator, 'rewards.1.sequence');

        $oversizedReward = $this->validRewardPayload();
        $oversizedReward['rewards'][0]['reward_id'] = 4294967296;
        [, $wireValidator] = $this->validatorFor($oversizedReward);
        $this->assertValidationError($wireValidator, 'rewards.0.reward_id');

        $duplicateRestriction = $this->validPayload();
        $duplicateRestriction['restrictions'] = [
            ['restriction_id' => 100, 'requires_completed' => 1],
            ['restriction_id' => 100, 'requires_completed' => 0],
        ];
        [, $restrictionValidator] = $this->validatorFor($duplicateRestriction);
        $this->assertValidationError(
            $restrictionValidator,
            'restrictions.1.restriction_id'
        );
    }

    private function validPayload(): array
    {
        return [
            'id' => 100,
            'name' => 'Test Achievement',
            'description' => 'A test definition',
            'icon_id' => 0,
            'points' => 10,
            'has_reward' => 0,
            'client_flag' => 0,
            'version' => 0,
            'reset_on_version_change' => 1,
            'enabled' => 1,
            'associations' => [[
                'category_id' => 10,
                'sequence' => 0,
                'display_text' => '',
            ]],
            'components' => [[
                'component_type' => 1,
                'sequence' => 0,
                'component_id' => 500,
                'name' => 'Do the thing',
                'description' => 'Complete this component.',
                'presentation_count' => 1,
                'criteria' => [[
                    'id' => null,
                    'event_type' => 0,
                    'progress_mode' => 0,
                    'behavior' => 0,
                    'target_id' => 0,
                    'target_id2' => 0,
                    'target_value' => 0,
                    'required_count' => 1,
                    'enabled' => 1,
                ]],
            ]],
            'rewards' => [],
            'reward_set' => null,
            'restrictions' => [],
        ];
    }

    private function payloadWithCriterion(
        int $eventType,
        int $progressMode,
        int $targetId,
        int $targetId2,
        int $targetValue
    ): array {
        $payload = $this->validPayload();
        $criterion = &$payload['components'][0]['criteria'][0];
        $criterion['event_type'] = $eventType;
        $criterion['progress_mode'] = $progressMode;
        $criterion['target_id'] = $targetId;
        $criterion['target_id2'] = $targetId2;
        $criterion['target_value'] = $targetValue;
        unset($criterion);

        return $payload;
    }

    private function validRewardPayload(): array
    {
        $payload = $this->validPayload();
        $payload['rewards'] = [[
            'reward_id' => null,
            'sequence' => 0,
            'reward_type' => 0,
            'reward_data_id' => 1001,
            'amount' => 1,
            'description' => 'A reward',
            'enabled' => 1,
            'option_id' => 1,
        ]];
        $payload['reward_set'] = [
            'present' => 1,
            'reward_set_id' => null,
            'title' => 'Choose a reward',
            'enabled' => 1,
            'source_enabled' => 1,
            'options' => [[
                'option_id' => 1,
                'sequence' => 0,
                'label' => 'Option one',
                'common_to_all' => 0,
                'flags' => 0,
                'enabled' => 1,
            ]],
        ];

        return $payload;
    }

    /**
     * @return array{0: ExposedAchievementRequest, 1: Validator}
     */
    private function validatorFor(array $payload, array $routeParameters = []): array
    {
        $request = ExposedAchievementRequest::create('/achievements', 'POST', $payload);
        $request->setContainer($this->app);
        $request->setRouteResolver(
            static fn () => new RequestRouteStub($routeParameters)
        );
        $request->prepareForTest();

        $validator = ValidatorFacade::make(
            $request->all(),
            $request->rules(),
            $request->messages(),
            $request->attributes()
        );
        foreach ($request->after() as $callback) {
            $validator->after($callback);
        }

        return [$request, $validator];
    }

    private function assertValidatorPasses(Validator $validator): void
    {
        $this->assertTrue(
            $validator->passes(),
            json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT) ?: ''
        );
    }

    private function assertValidationError(Validator $validator, string $key): void
    {
        $this->assertTrue($validator->fails(), "Expected validation to fail for {$key}.");
        $this->assertArrayHasKey(
            $key,
            $validator->errors()->toArray(),
            json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT) ?: ''
        );
    }
}

final class ExposedAchievementRequest extends AchievementRequest
{
    public function prepareForTest(): void
    {
        $this->prepareForValidation();
    }
}

final class RequestRouteStub
{
    public function __construct(private readonly array $parameters) {}

    public function parameter(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }
}
