<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller {
  public function __construct(private readonly CategoryService $categoryService) {
  }

  public function index(): \Inertia\Response {
    return inertia('Categories', [
      'categories' => $this->categoryService->getPaginatedCategories(),
    ]);
  }

  public function store(StoreCategoryRequest $request): RedirectResponse {
    $this->categoryService->createCategory($request->validated());

    return redirect()->back()->with('success', 'Категорията беше добавена успешно.');
  }

  public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse {
    $this->categoryService->updateCategory($category, $request->validated());

    return redirect()->back()->with('success', 'Категорията беше обновена успешно.');
  }

  public function destroy(Category $category): RedirectResponse {
    $this->categoryService->deleteCategory($category);

    return redirect()->back()->with('success', 'Категорията беше изтрита успешно.');
  }
}
