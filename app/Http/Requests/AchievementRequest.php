<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;

class AchievementRequest extends BaseRequest
{
    private const UINT32_MAX = 4294967295;

    private const HIGHEST_SKILL = 77;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon_id' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'points' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'reward_display' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'world_display_flag' => ['required', 'integer', 'min:0', 'max:255'],
            'definition_version' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'reset_on_version_change' => ['required', 'boolean'],
            'enabled' => ['required', 'boolean'],

            'associations' => ['present', 'array'],
            'associations.*.category_id' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'associations.*.sequence' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'associations.*.display_text' => ['nullable', 'string', 'max:255'],

            'components' => ['present', 'array'],
            'components.*.component_type' => ['required', 'integer', 'between:0,3'],
            'components.*.sequence' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'components.*.component_id' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'components.*.description' => ['nullable', 'string'],
            'components.*.description_2' => ['nullable', 'string'],
            'components.*.presentation_count' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'components.*.criteria' => ['present', 'array'],
            'components.*.criteria.*.id' => ['nullable', 'integer', 'min:1'],
            'components.*.criteria.*.event_type' => ['required', 'integer', 'between:0,13'],
            'components.*.criteria.*.progress_mode' => ['required', 'integer', 'between:0,3'],
            'components.*.criteria.*.behavior' => ['required', 'integer', 'between:0,5'],
            'components.*.criteria.*.target_id' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'components.*.criteria.*.target_id2' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'components.*.criteria.*.target_value' => ['required', 'integer', 'min:0'],
            'components.*.criteria.*.required_count' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'components.*.criteria.*.enabled' => ['required', 'boolean'],

            'rewards' => ['present', 'array'],
            'rewards.*.reward_id' => ['nullable', 'integer', 'min:1'],
            'rewards.*.sequence' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'rewards.*.reward_type' => ['required', 'integer', 'between:0,5'],
            'rewards.*.reward_data_id' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'rewards.*.amount' => ['required', 'integer', 'min:1'],
            'rewards.*.description' => ['nullable', 'string', 'max:255'],
            'rewards.*.enabled' => ['required', 'boolean'],
            'rewards.*.option_id' => ['nullable', 'integer', 'min:1', 'max:'.self::UINT32_MAX],

            'reward_set' => ['nullable', 'array'],
            'reward_set.reward_set_id' => ['nullable', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'reward_set.title' => ['nullable', 'string', 'max:255'],
            'reward_set.enabled' => ['required_with:reward_set', 'boolean'],
            'reward_set.options' => ['required_with:reward_set', 'array'],
            'reward_set.options.*.option_id' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'reward_set.options.*.sequence' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'reward_set.options.*.label' => ['nullable', 'string', 'max:255'],
            'reward_set.options.*.common_to_all' => ['required', 'boolean'],
            'reward_set.options.*.flags' => ['required', 'integer', 'between:0,255'],
            'reward_set.options.*.enabled' => ['required', 'boolean'],

            'restrictions' => ['present', 'array'],
            'restrictions.*.restriction_id' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'restrictions.*.requires_completed' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $associations = is_array($this->input('associations')) ? $this->input('associations') : [];
        $components = is_array($this->input('components')) ? $this->input('components') : [];
        $rewards = is_array($this->input('rewards')) ? $this->input('rewards') : [];
        $restrictions = is_array($this->input('restrictions')) ? $this->input('restrictions') : [];
        $rewardSet = $this->input('reward_set');

        foreach ($components as &$component) {
            $component['criteria'] = is_array($component['criteria'] ?? null)
                ? $component['criteria']
                : [];
            foreach ($component['criteria'] as &$criterion) {
                $criterion['target_id'] = $this->integerValue($criterion['target_id'] ?? 0, 0);
                $criterion['target_id2'] = $this->integerValue($criterion['target_id2'] ?? 0, 0);
                $criterion['target_value'] = $this->integerValue($criterion['target_value'] ?? 0, 0);
                $criterion['required_count'] = $this->integerValue($criterion['required_count'] ?? 1, 1);
                $criterion['enabled'] = $this->booleanValue($criterion['enabled'] ?? false);
                if (($criterion['id'] ?? null) === '') {
                    $criterion['id'] = null;
                }
            }
            unset($criterion);
        }
        unset($component);

        foreach ($rewards as &$reward) {
            $reward['reward_data_id'] = $this->integerValue($reward['reward_data_id'] ?? 0, 0);
            $reward['amount'] = $this->integerValue($reward['amount'] ?? 1, 1);
            $reward['enabled'] = $this->booleanValue($reward['enabled'] ?? false);
            if (($reward['reward_id'] ?? null) === '') {
                $reward['reward_id'] = null;
            }
            if (($reward['option_id'] ?? null) === '') {
                $reward['option_id'] = null;
            }
        }
        unset($reward);

        if (is_array($rewardSet)) {
            $present = ! array_key_exists('present', $rewardSet)
                || $this->booleanValue($rewardSet['present']) === 1;
            if (! $present) {
                $rewardSet = null;
            } else {
                if (($rewardSet['reward_set_id'] ?? null) === '') {
                    $rewardSet['reward_set_id'] = null;
                }
                $rewardSet['enabled'] = $this->booleanValue($rewardSet['enabled'] ?? false);
                $rewardSet['options'] = is_array($rewardSet['options'] ?? null)
                    ? $rewardSet['options']
                    : [];
                foreach ($rewardSet['options'] as &$option) {
                    $option['common_to_all'] = $this->booleanValue($option['common_to_all'] ?? false);
                    $option['enabled'] = $this->booleanValue($option['enabled'] ?? false);
                }
                unset($option);
            }
        } else {
            $rewardSet = null;
        }

        foreach ($restrictions as &$restriction) {
            $restriction['requires_completed'] = $this->booleanValue(
                $restriction['requires_completed'] ?? false
            );
        }
        unset($restriction);

        $this->merge([
            'description' => (string) $this->input('description', ''),
            'icon_id' => $this->defaultInt('icon_id', 0),
            'points' => $this->defaultInt('points', 0),
            'reward_display' => $this->defaultInt('reward_display', 0),
            'world_display_flag' => $this->defaultInt('world_display_flag', 0),
            'definition_version' => $this->defaultInt('definition_version', 1),
            'reset_on_version_change' => $this->boolean('reset_on_version_change') ? 1 : 0,
            'enabled' => $this->boolean('enabled') ? 1 : 0,
            'associations' => $associations,
            'components' => $components,
            'rewards' => $rewards,
            'reward_set' => $rewardSet,
            'restrictions' => $restrictions,
        ]);
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $data = $this->all();
            $this->validateStableAchievementId($validator, $data);
            $this->validateAssociations($validator, $data);
            $this->validateComponentsAndCriteria($validator, $data);
            $this->validateRewards($validator, $data);
            $this->validateRestrictions($validator, $data);
        }];
    }

    private function validateStableAchievementId(Validator $validator, array $data): void
    {
        $routeValue = $this->route('achievement');
        if (is_object($routeValue)) {
            $routeValue = $routeValue->getRouteKey();
        }

        if ($routeValue !== null && (int) $routeValue !== (int) ($data['id'] ?? 0)) {
            $validator->errors()->add('id', 'The stable achievement ID cannot be changed after creation.');
        }
    }

    private function validateAssociations(Validator $validator, array $data): void
    {
        $seen = [];
        foreach (($data['associations'] ?? []) as $index => $association) {
            $categoryId = (int) ($association['category_id'] ?? 0);
            if (isset($seen[$categoryId])) {
                $validator->errors()->add(
                    "associations.{$index}.category_id",
                    'An achievement may be associated with a category only once.'
                );
            }
            $seen[$categoryId] = true;
        }

        if ((int) ($data['enabled'] ?? 0) === 1 && $seen === []) {
            $validator->errors()->add(
                'associations',
                'An enabled achievement must have at least one category association.'
            );
        }
    }

    private function validateComponentsAndCriteria(Validator $validator, array $data): void
    {
        $componentKeys = [];
        $presentationCounts = [];
        $requiredClasses = [];

        foreach (($data['components'] ?? []) as $componentIndex => $component) {
            $componentType = (int) ($component['component_type'] ?? -1);
            $componentId = (int) ($component['component_id'] ?? -1);
            $componentKey = "{$componentType}:{$componentId}";
            if (isset($componentKeys[$componentKey])) {
                $validator->errors()->add(
                    "components.{$componentIndex}.component_id",
                    'Component identity must be unique within the achievement.'
                );
            }
            $componentKeys[$componentKey] = true;

            $presentationCount = (int) ($component['presentation_count'] ?? 1);
            if (isset($presentationCounts[$componentId]) && $presentationCounts[$componentId] !== $presentationCount) {
                $validator->errors()->add(
                    "components.{$componentIndex}.presentation_count",
                    'Components sharing a component ID must share one global presentation count.'
                );
            }
            $presentationCounts[$componentId] = $presentationCount;

            $criterionIdentities = [];
            $enabledPolicy = null;
            foreach (($component['criteria'] ?? []) as $criterionIndex => $criterion) {
                $path = "components.{$componentIndex}.criteria.{$criterionIndex}";
                $eventType = (int) ($criterion['event_type'] ?? -1);
                $progressMode = (int) ($criterion['progress_mode'] ?? -1);
                $behavior = (int) ($criterion['behavior'] ?? -1);
                $targetId = (int) ($criterion['target_id'] ?? 0);
                $targetId2 = (int) ($criterion['target_id2'] ?? 0);
                $targetValue = (int) ($criterion['target_value'] ?? 0);
                $requiredCount = (int) ($criterion['required_count'] ?? 0);
                $enabled = (int) ($criterion['enabled'] ?? 0) === 1;

                $criterionIdentity = "{$eventType}:{$targetId}:{$targetId2}";
                if (isset($criterionIdentities[$criterionIdentity])) {
                    $validator->errors()->add(
                        "{$path}.target_id",
                        'Criterion event and target identity must be unique for this component.'
                    );
                }
                $criterionIdentities[$criterionIdentity] = true;

                if (! $enabled) {
                    continue;
                }

                if ($componentType === 3) {
                    $validator->errors()->add(
                        "{$path}.enabled",
                        'RoF2 component type 3 is presentation-only and cannot have an enabled criterion.'
                    );
                }

                $policy = [$eventType, $progressMode, $behavior, $requiredCount];
                if ($enabledPolicy !== null && $enabledPolicy !== $policy) {
                    $validator->errors()->add(
                        $path,
                        'Enabled alternative criteria must agree on event, mode, behavior, and required count.'
                    );
                }
                $enabledPolicy ??= $policy;

                if ($targetId2 !== 0 && ! in_array($eventType, [7, 12, 13], true)) {
                    $validator->errors()->add(
                        "{$path}.target_id2",
                        'This event does not support a secondary target.'
                    );
                }
                if ($eventType === 12 && $targetId === 0) {
                    $validator->errors()->add(
                        "{$path}.target_id",
                        'NPC-name kill criteria require a nonzero canonical-name hash.'
                    );
                }
                if ($eventType === 4 && $targetId === 0) {
                    $validator->errors()->add(
                        "{$path}.target_id",
                        'Task-complete criteria require a specific nonzero task ID.'
                    );
                }
                if (in_array($eventType, [1, 10], true) && $targetId !== 0) {
                    $validator->errors()->add(
                        "{$path}.target_id",
                        'Level and alternate-advancement criteria must use target ID zero.'
                    );
                }
                if ($eventType === 7 && $targetId2 !== 0 && ($targetId2 < 1 || $targetId2 > 16)) {
                    $validator->errors()->add(
                        "{$path}.target_id2",
                        'Own-item class must be zero or a valid EQ class from 1 through 16.'
                    );
                }
                if ($eventType === 9 && $targetId !== self::UINT32_MAX && $targetId > self::HIGHEST_SKILL) {
                    $validator->errors()->add(
                        "{$path}.target_id",
                        'Skill Value requires skill 0 through 77, or 4294967295 for the wildcard.'
                    );
                }
                if (
                    $eventType === 13
                    && (
                        $targetId > self::HIGHEST_SKILL
                        || $targetId2 < 1
                        || $targetId2 > 16
                        || $targetValue < 1
                        || $targetValue > 255
                    )
                ) {
                    $validator->errors()->add(
                        $path,
                        'Skill Cap requires skill 0 through 77, class 1 through 16, and milestone level 1 through 255.'
                    );
                }

                $absoluteEvent = in_array($eventType, [1, 7, 9, 10, 13], true);
                $replayedSpecificEvent = $eventType === 4 && $targetId !== 0;
                $replayedAchievementEvent = $eventType === 11;
                if ($progressMode === 0 && ($absoluteEvent || $replayedSpecificEvent || $replayedAchievementEvent)) {
                    $validator->errors()->add(
                        "{$path}.progress_mode",
                        'Increment mode cannot be used for reconciled absolute or one-time events.'
                    );
                }
                if ($progressMode === 3 && $absoluteEvent && $targetValue < 1) {
                    $validator->errors()->add(
                        "{$path}.target_value",
                        'Boolean mode requires a positive target value for an absolute event.'
                    );
                }

                $completionPolicy = ! in_array($behavior, [1, 4, 5], true);
                if (
                    $completionPolicy
                    && in_array($eventType, [7, 13], true)
                    && $targetId2 >= 1
                    && $targetId2 <= 16
                ) {
                    $requiredClasses[$targetId2] = true;
                }
            }
        }

        if (count($requiredClasses) > 1) {
            $validator->errors()->add(
                'components',
                'Required, unlock, and visibility class criteria must agree on one EQ class.'
            );
        }
    }

    private function validateRewards(Validator $validator, array $data): void
    {
        $rewardIds = [];
        $rewardSequences = [];
        $optionIds = [];
        $enabledOptionRewards = [];
        $rewardSet = $data['reward_set'] ?? null;

        if (is_array($rewardSet)) {
            foreach (($rewardSet['options'] ?? []) as $index => $option) {
                $optionId = (int) ($option['option_id'] ?? 0);
                if (isset($optionIds[$optionId])) {
                    $validator->errors()->add(
                        "reward_set.options.{$index}.option_id",
                        'Option IDs must be unique within a reward set.'
                    );
                }
                $optionIds[$optionId] = $option;
            }
        }

        foreach (($data['rewards'] ?? []) as $index => $reward) {
            $rewardId = $reward['reward_id'] ?? null;
            $sequence = (int) ($reward['sequence'] ?? 0);
            $rewardType = (int) ($reward['reward_type'] ?? -1);
            $rewardDataId = (int) ($reward['reward_data_id'] ?? 0);
            $enabled = (int) ($reward['enabled'] ?? 0) === 1;
            $optionId = $reward['option_id'] ?? null;

            if ($rewardId !== null) {
                $rewardId = (string) $rewardId;
                if (isset($rewardIds[$rewardId])) {
                    $validator->errors()->add(
                        "rewards.{$index}.reward_id",
                        'A canonical reward ID may appear only once.'
                    );
                }
                $rewardIds[$rewardId] = true;
            }
            if (isset($rewardSequences[$sequence])) {
                $validator->errors()->add(
                    "rewards.{$index}.sequence",
                    'Reward sequence must be unique within the achievement.'
                );
            }
            $rewardSequences[$sequence] = true;

            if ($enabled && in_array($rewardType, [0, 4, 5], true) && $rewardDataId === 0) {
                $validator->errors()->add(
                    "rewards.{$index}.reward_data_id",
                    'Enabled item, alternate-currency, and title rewards require a nonzero data ID.'
                );
            }
            if ($enabled && $rewardType === 1 && $rewardDataId > 1) {
                $validator->errors()->add(
                    "rewards.{$index}.reward_data_id",
                    'Experience reward mode must be 0 (normal handling) or 1 (normal-only raw XP).'
                );
            }
            if ($enabled && $rewardId !== null && (int) $rewardId > self::UINT32_MAX) {
                $validator->errors()->add(
                    "rewards.{$index}.reward_id",
                    'Enabled reward IDs must fit the unsigned 32-bit RoF2 wire field.'
                );
            }

            if ($optionId !== null) {
                $optionId = (int) $optionId;
                if (! is_array($rewardSet) || ! isset($optionIds[$optionId])) {
                    $validator->errors()->add(
                        "rewards.{$index}.option_id",
                        'A mapped reward must reference an option in this achievement reward set.'
                    );
                }
                if ($enabled) {
                    $enabledOptionRewards[$optionId] = true;
                }
            }
        }

        if (! is_array($rewardSet) || (int) ($rewardSet['enabled'] ?? 0) !== 1) {
            return;
        }

        $hasSelectableOption = false;
        $hasEnabledOption = false;
        foreach (($rewardSet['options'] ?? []) as $index => $option) {
            if ((int) ($option['enabled'] ?? 0) !== 1) {
                continue;
            }
            $hasEnabledOption = true;
            $optionId = (int) ($option['option_id'] ?? 0);
            $hasSelectableOption = $hasSelectableOption || (int) ($option['common_to_all'] ?? 0) === 0;
            if (! isset($enabledOptionRewards[$optionId])) {
                $validator->errors()->add(
                    "reward_set.options.{$index}",
                    'Every enabled reward option must contain at least one enabled grant.'
                );
            }
        }

        if (! $hasEnabledOption || ! $hasSelectableOption) {
            $validator->errors()->add(
                'reward_set.options',
                'An enabled reward set requires at least one enabled, non-common selectable option.'
            );
        }
    }

    private function validateRestrictions(Validator $validator, array $data): void
    {
        $restrictionIds = [];
        foreach (($data['restrictions'] ?? []) as $index => $restriction) {
            $restrictionId = (int) ($restriction['restriction_id'] ?? 0);
            if (isset($restrictionIds[$restrictionId])) {
                $validator->errors()->add(
                    "restrictions.{$index}.restriction_id",
                    'A restriction ID may be attached to this achievement only once.'
                );
            }
            $restrictionIds[$restrictionId] = true;
        }
    }

    private function integerValue(mixed $value, int $default): mixed
    {
        return $value === '' || $value === null ? $default : $value;
    }

    private function booleanValue(mixed $value): int
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    }
}
