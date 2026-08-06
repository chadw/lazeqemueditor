<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\AchievementCategoryRequest;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Tests\TestCase;

class AchievementCategoryRequestTest extends TestCase
{
    public function test_optional_form_values_are_normalized_to_schema_defaults(): void
    {
        [$request, $validator] = $this->validatorFor([
            'id' => 10,
            'parent_id' => '',
            'sequence' => '',
            'name' => 'General',
            'description' => null,
            'icon' => null,
        ]);

        $this->assertTrue(
            $validator->passes(),
            json_encode($validator->errors()->toArray(), JSON_PRETTY_PRINT) ?: ''
        );
        $this->assertSame(0, $request->input('parent_id'));
        $this->assertSame(0, $request->input('sequence'));
        $this->assertSame('', $request->input('description'));
        $this->assertSame('', $request->input('icon'));
    }

    public function test_category_cannot_be_its_own_parent(): void
    {
        [, $validator] = $this->validatorFor([
            'id' => 10,
            'parent_id' => 10,
            'sequence' => 0,
            'name' => 'Cycle',
        ]);

        $this->assertValidationError($validator, 'parent_id');
    }

    public function test_stable_category_id_cannot_change_on_update(): void
    {
        [, $validator] = $this->validatorFor([
            'id' => 10,
            'parent_id' => 0,
            'sequence' => 0,
            'name' => 'General',
        ], ['achievement_category' => 11]);

        $this->assertValidationError($validator, 'id');
    }

    public function test_category_ids_respect_the_unsigned_32_bit_wire_boundary(): void
    {
        [, $maximumValidator] = $this->validatorFor([
            'id' => 4294967295,
            'parent_id' => 0,
            'sequence' => 4294967295,
            'name' => 'Maximum',
        ]);
        $this->assertTrue(
            $maximumValidator->passes(),
            json_encode($maximumValidator->errors()->toArray(), JSON_PRETTY_PRINT) ?: ''
        );

        [, $overflowValidator] = $this->validatorFor([
            'id' => 4294967296,
            'parent_id' => 0,
            'sequence' => 0,
            'name' => 'Overflow',
        ]);
        $this->assertValidationError($overflowValidator, 'id');
    }

    /**
     * @return array{0: ExposedAchievementCategoryRequest, 1: Validator}
     */
    private function validatorFor(array $payload, array $routeParameters = []): array
    {
        $request = ExposedAchievementCategoryRequest::create(
            '/achievement-categories',
            'POST',
            $payload
        );
        $request->setContainer($this->app);
        $request->setRouteResolver(
            static fn () => new CategoryRouteStub($routeParameters)
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

final class ExposedAchievementCategoryRequest extends AchievementCategoryRequest
{
    public function prepareForTest(): void
    {
        $this->prepareForValidation();
    }
}

final class CategoryRouteStub
{
    public function __construct(private readonly array $parameters) {}

    public function parameter(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }
}
