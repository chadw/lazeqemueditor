<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementCategoryRequest;
use App\Models\AchievementCategory;
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

    public function store(AchievementCategoryRequest $request)
    {
        $data = $request->validated();

        $model = AchievementCategory::create($data);

        toast()->success('Saved!', 'Achievement Category created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('achievement-categories.index'),
        ], 201);
    }

    public function update(AchievementCategoryRequest $request, AchievementCategory $achievement_category)
    {
        $data = $request->validated();

        $achievement_category->update($data);

        toast()->success('Saved!', "Achievement Category {$achievement_category->id} updated.");

        return response()->json([
            'success' => true,
            'data'    => $achievement_category,
            'redirect'=> route('achievement-categories.index'),
        ], 201);
    }

    public function destroy(AchievementCategory $achievement_category)
    {
        $achievement_category->delete();

        return back()->with('success', "Achievement Category {$achievement_category->id} deleted.");
    }
}
