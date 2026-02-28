<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService {
  public function getPaginatedCategories(): LengthAwarePaginator {
    return Category::query()
      ->withCount('expenses')
      ->orderBy('name')
      ->paginate(10)
      ->withQueryString();
  }

  /**
   * @param array<string, mixed> $validatedData
   */
  public function createCategory(array $validatedData): Category {
    return Category::query()->create($validatedData);
  }

  /**
   * @param array<string, mixed> $validatedData
   */
  public function updateCategory(Category $category, array $validatedData): bool {
    return $category->update($validatedData);
  }

  public function deleteCategory(Category $category): bool {
    return (bool) $category->delete();
  }
}
