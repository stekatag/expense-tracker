<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ExpenseController extends Controller {
    public function __construct(private readonly ExpenseService $expenseService) {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request): RedirectResponse {
        $this->expenseService->createExpense($request->user(), $request->validated());

        return redirect()->back()->with('success', 'Разходът беше добавен успешно.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse {
        $this->expenseService->updateExpense($expense, $request->validated());

        return redirect()->back()->with('success', 'Разходът беше обновен успешно.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Expense $expense): RedirectResponse {
        abort_if($expense->user_id !== $request->user()->id, 403);

        $this->expenseService->deleteExpense($expense);

        return redirect()->back()->with('success', 'Разходът беше изтрит успешно.');
    }
}
