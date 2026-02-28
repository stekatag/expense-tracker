<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpenseService {
  /**
   * @param array<string, mixed> $validatedFilters
   * @return array<string, mixed>
   */
  public function buildDashboardData(User $user, array $validatedFilters): array {
    $baseQuery = $user
      ->expenses()
      ->with('category')
      ->when(
        $validatedFilters['category_id'] ?? null,
        fn($query, $categoryId) => $query->where('category_id', $categoryId),
      )
      ->when(
        $validatedFilters['from'] ?? null,
        fn($query, $from) => $query->whereDate('date', '>=', $from),
      )
      ->when(
        $validatedFilters['to'] ?? null,
        fn($query, $to) => $query->whereDate('date', '<=', $to),
      );

    $totalAmount = (float) (clone $baseQuery)->sum('amount');
    $expenseCount = (clone $baseQuery)->count();
    $averageAmount = $expenseCount > 0 ? $totalAmount / $expenseCount : 0.0;

    $expenses = $this->paginateExpenses((clone $baseQuery)->latest('date')->latest('id'));

    return [
      'expenses' => $expenses,
      'categories' => Category::query()->orderBy('name')->get(),
      'filters' => [
        'category_id' => $validatedFilters['category_id'] ?? null,
        'from' => $validatedFilters['from'] ?? null,
        'to' => $validatedFilters['to'] ?? null,
      ],
      'stats' => [
        'total_amount' => round($totalAmount, 2),
        'average_amount' => round($averageAmount, 2),
        'count' => $expenseCount,
      ],
    ];
  }

  /**
   * @param \Illuminate\Database\Eloquent\Builder<Expense> $query
   */
  private function paginateExpenses($query): LengthAwarePaginator {
    return $query->paginate(10)->withQueryString();
  }

  /**
   * @param array<string, mixed> $validatedData
   */
  public function createExpense(User $user, array $validatedData): Expense {
    return $user->expenses()->create($validatedData);
  }

  /**
   * @param array<string, mixed> $validatedData
   */
  public function updateExpense(Expense $expense, array $validatedData): bool {
    return $expense->update($validatedData);
  }

  public function deleteExpense(Expense $expense): bool {
    return (bool) $expense->delete();
  }
}
