<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('displays the dashboard with expenses', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Expense::factory()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $this->actingAs($user)
    ->get('/dashboard')
    ->assertSuccessful();
});

it('allows user to create an expense', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $this->actingAs($user)
    ->post('/expenses', [
      'amount' => 15.50,
      'description' => 'Test expense',
      'date' => now()->format('Y-m-d'),
      'category_id' => $category->id,
    ])
    ->assertRedirect();

  $this->assertDatabaseHas('expenses', [
    'user_id' => $user->id,
    'description' => 'Test expense',
    'amount' => 15.50,
  ]);
});

it('prevents user from updating anothers expense', function () {
  $user1 = User::factory()->create();
  $user2 = User::factory()->create();
  $category = Category::factory()->create();

  $expense = Expense::factory()->create([
    'user_id' => $user1->id,
    'category_id' => $category->id,
  ]);

  $this->actingAs($user2)
    ->patch("/expenses/{$expense->id}", [
      'amount' => 20.00,
      'description' => 'Hacked',
    ])
    ->assertForbidden();

  $this->assertDatabaseMissing('expenses', [
    'id' => $expense->id,
    'amount' => 20.00,
  ]);
});

it('allows user to update their own expense', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $expense = Expense::factory()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
    'description' => 'Старо описание',
    'amount' => 12.00,
    'date' => now()->subDay()->format('Y-m-d'),
  ]);

  $this->actingAs($user)
    ->patch("/expenses/{$expense->id}", [
      'description' => 'Ново описание',
      'amount' => 25.70,
      'category_id' => $category->id,
      'date' => now()->format('Y-m-d'),
    ])
    ->assertRedirect();

  $this->assertDatabaseHas('expenses', [
    'id' => $expense->id,
    'description' => 'Ново описание',
    'amount' => 25.70,
  ]);
});

it('prevents user from deleting anothers expense', function () {
  $user1 = User::factory()->create();
  $user2 = User::factory()->create();
  $category = Category::factory()->create();

  $expense = Expense::factory()->create([
    'user_id' => $user1->id,
    'category_id' => $category->id,
  ]);

  $this->actingAs($user2)
    ->delete("/expenses/{$expense->id}")
    ->assertForbidden();

  $this->assertDatabaseHas('expenses', [
    'id' => $expense->id,
  ]);
});

it('allows user to delete their own expense', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $expense = Expense::factory()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $this->actingAs($user)
    ->delete("/expenses/{$expense->id}")
    ->assertRedirect();

  $this->assertDatabaseMissing('expenses', [
    'id' => $expense->id,
  ]);
});

it('filters expenses by category and date range in dashboard', function () {
  $user = User::factory()->create();
  $food = Category::factory()->create(['name' => 'Храна']);
  $transport = Category::factory()->create(['name' => 'Транспорт']);

  $matchingExpense = Expense::factory()->create([
    'user_id' => $user->id,
    'category_id' => $food->id,
    'description' => 'Обяд',
    'date' => '2026-02-10',
  ]);

  Expense::factory()->create([
    'user_id' => $user->id,
    'category_id' => $transport->id,
    'description' => 'Билет',
    'date' => '2026-02-10',
  ]);

  Expense::factory()->create([
    'user_id' => $user->id,
    'category_id' => $food->id,
    'description' => 'Вечеря',
    'date' => '2026-01-25',
  ]);

  $this->actingAs($user)
    ->get('/dashboard?category_id=' . $food->id . '&from=2026-02-01&to=2026-02-28')
    ->assertSuccessful()
    ->assertInertia(
      fn(AssertableInertia $page) => $page
        ->where('filters.category_id', (string) $food->id)
        ->where('filters.from', '2026-02-01')
        ->where('filters.to', '2026-02-28')
        ->has('expenses.data', 1)
        ->where('expenses.data.0.id', $matchingExpense->id)
    );
});

it('paginates expenses while keeping filters in query string', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  Expense::factory()->count(12)->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
    'date' => '2026-02-20',
  ]);

  $this->actingAs($user)
    ->get('/dashboard?category_id=' . $category->id)
    ->assertSuccessful()
    ->assertInertia(
      fn(AssertableInertia $page) => $page
        ->where('filters.category_id', (string) $category->id)
        ->where('expenses.current_page', 1)
        ->where('expenses.last_page', 2)
        ->has('expenses.data', 10)
    );

  $this->actingAs($user)
    ->get('/dashboard?category_id=' . $category->id . '&page=2')
    ->assertSuccessful()
    ->assertInertia(
      fn(AssertableInertia $page) => $page
        ->where('filters.category_id', (string) $category->id)
        ->where('expenses.current_page', 2)
        ->has('expenses.data', 2)
    );
});
