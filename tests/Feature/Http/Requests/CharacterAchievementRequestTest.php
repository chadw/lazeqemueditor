<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\DiscardCharacterAchievementMutationRequest;
use App\Http\Requests\ForceCompleteCharacterAchievementRequest;
use App\Http\Requests\MarkCharacterAchievementRewardRetryableRequest;
use App\Http\Requests\ResetCharacterAchievementRequest;
use App\Http\Requests\SetCharacterAchievementProgressRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Tests\TestCase;

class CharacterAchievementRequestTest extends TestCase
{
    public function test_destructive_actions_require_explicit_acknowledgement(): void
    {
        $cases = [
            [ForceCompleteCharacterAchievementRequest::class, 'confirm_offline_completion'],
            [ResetCharacterAchievementRequest::class, 'confirm_reset'],
            [MarkCharacterAchievementRewardRetryableRequest::class, 'confirm_retry'],
            [DiscardCharacterAchievementMutationRequest::class, 'confirm_discard'],
        ];

        foreach ($cases as [$requestClass, $field]) {
            $missing = $this->validatorFor($requestClass, []);
            $this->assertValidationError($missing, $field);

            $declined = $this->validatorFor($requestClass, [$field => '0']);
            $this->assertValidationError($declined, $field);

            $accepted = $this->validatorFor($requestClass, [$field => 'yes']);
            $this->assertTrue(
                $accepted->passes(),
                json_encode($accepted->errors()->toArray(), JSON_PRETTY_PRINT) ?: ''
            );
        }
    }

    public function test_progress_accepts_zero_and_rejects_negative_or_fractional_counts(): void
    {
        foreach ([0, '5'] as $value) {
            $validator = $this->validatorFor(
                SetCharacterAchievementProgressRequest::class,
                ['current_count' => $value]
            );
            $this->assertTrue(
                $validator->passes(),
                json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT) ?: ''
            );
        }

        foreach ([-1, '1.5', 'not-a-count'] as $value) {
            $validator = $this->validatorFor(
                SetCharacterAchievementProgressRequest::class,
                ['current_count' => $value]
            );
            $this->assertValidationError($validator, 'current_count');
        }
    }

    public function test_reward_reset_flag_is_optional_but_must_be_boolean_when_present(): void
    {
        $withoutFlag = $this->validatorFor(
            ResetCharacterAchievementRequest::class,
            ['confirm_reset' => 'yes']
        );
        $this->assertTrue($withoutFlag->passes());

        foreach ([true, false, 0, 1, '0', '1'] as $value) {
            $validator = $this->validatorFor(
                ResetCharacterAchievementRequest::class,
                ['confirm_reset' => 'yes', 'reset_rewards' => $value]
            );
            $this->assertTrue(
                $validator->passes(),
                json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT) ?: ''
            );
        }

        $invalid = $this->validatorFor(
            ResetCharacterAchievementRequest::class,
            ['confirm_reset' => 'yes', 'reset_rewards' => 'sometimes']
        );
        $this->assertValidationError($invalid, 'reset_rewards');
    }

    /**
     * @param  class-string<FormRequest>  $requestClass
     */
    private function validatorFor(string $requestClass, array $payload): Validator
    {
        /** @var FormRequest $request */
        $request = new $requestClass;

        return ValidatorFacade::make(
            $payload,
            $request->rules(),
            $request->messages(),
            $request->attributes()
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
