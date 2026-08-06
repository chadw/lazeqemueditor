<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementCategoryRequest;
use App\Services\AchievementAggregateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementCategoryController extends Controller
{
    public function __construct(private readonly AchievementAggregateService $achievements) {}

    public function index(Request $request): View
    {
        $categoryRows = $this->achievements->categoryRows();
        $editingId = $request->integer('edit');
        $editingCategory = null;
        if ($editingId > 0) {
            foreach ($categoryRows as $row) {
                if ((int) $row['id'] === $editingId) {
                    $editingCategory = (object) $row;
                    break;
                }
            }
        }

        return view('achievement-categories.index', [
            'categoryRows' => $categoryRows,
            'categories' => $this->achievements->categoryOptions($categoryRows),
            'editingCategory' => $editingCategory,
        ]);
    }

    public function store(AchievementCategoryRequest $request): RedirectResponse
    {
        $categoryId = $this->achievements->storeCategory($request->validated());
        toast()->success('Saved!', "Achievement category {$categoryId} created.");

        return redirect()->route('achievement-categories.index', ['edit' => $categoryId]);
    }

    public function update(
        AchievementCategoryRequest $request,
        mixed $achievement_category
    ): RedirectResponse {
        $categoryId = $this->routeId($achievement_category);
        $this->achievements->updateCategory($categoryId, $request->validated());
        toast()->success('Saved!', "Achievement category {$categoryId} updated.");

        return redirect()->route('achievement-categories.index', ['edit' => $categoryId]);
    }

    public function destroy(mixed $achievement_category): RedirectResponse
    {
        $categoryId = $this->routeId($achievement_category);
        $this->achievements->destroyCategory($categoryId);
        toast()->success('Deleted!', "Achievement category {$categoryId} deleted.");

        return redirect()->route('achievement-categories.index');
    }

    private function routeId(mixed $value): int
    {
        if (is_object($value)) {
            if (method_exists($value, 'getKey')) {
                return (int) $value->getKey();
            }
            if (isset($value->id)) {
                return (int) $value->id;
            }
        }

        return (int) $value;
    }
}
