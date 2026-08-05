<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;

class AchievementCategoryRequest extends BaseRequest
{
    private const UINT32_MAX = 4294967295;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'min:1', 'max:'.self::UINT32_MAX],
            'parent_id' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'sequence' => ['required', 'integer', 'min:0', 'max:'.self::UINT32_MAX],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'parent_id' => $this->defaultInt('parent_id', 0),
            'sequence' => $this->defaultInt('sequence', 0),
            'description' => (string) $this->input('description', ''),
            'icon' => (string) $this->input('icon', ''),
        ]);
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $id = (int) $this->input('id', 0);
            $parentId = (int) $this->input('parent_id', 0);
            if ($id !== 0 && $id === $parentId) {
                $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
            }

            $routeValue = $this->route('achievement_category') ?? $this->route('category');
            if (is_object($routeValue)) {
                $routeValue = $routeValue->getRouteKey();
            }
            if ($routeValue !== null && (int) $routeValue !== $id) {
                $validator->errors()->add('id', 'The stable category ID cannot be changed after creation.');
            }
        }];
    }
}
