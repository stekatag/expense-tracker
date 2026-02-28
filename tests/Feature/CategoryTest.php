<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects guests from categories page', function () {
  $this->get('/categories')->assertRedirect(route('login'));
});

it('shows categories page for authenticated user', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create(['name' => 'Храна']);

  $this->actingAs($user)
    ->get('/categories')
    ->assertSuccessful()
    ->assertInertia(
      fn(AssertableInertia $page) => $page
        ->component('Categories')
        ->has('categories.data', 1)
        ->where('categories.data.0.id', $category->id)
    );
});

it('allows authenticated user to create category', function () {
  $user = User::factory()->create();

  $this->actingAs($user)
    ->post('/categories', [
      'name' => 'Пазаруване',
    ])
    ->assertRedirect();

  $this->assertDatabaseHas('categories', [
    'name' => 'Пазаруване',
  ]);
});

it('allows authenticated user to update category', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create([
    'name' => 'Старо име',
  ]);

  $this->actingAs($user)
    ->patch("/categories/{$category->id}", [
      'name' => 'Ново име',
    ])
    ->assertRedirect();

  $this->assertDatabaseHas('categories', [
    'id' => $category->id,
    'name' => 'Ново име',
  ]);
});

it('allows authenticated user to delete category and cascades expenses', function () {
  $user = User::factory()->create();
  $category = Category::factory()->create();

  $expense = Expense::factory()->create([
    'user_id' => $user->id,
    'category_id' => $category->id,
  ]);

  $this->actingAs($user)
    ->delete("/categories/{$category->id}")
    ->assertRedirect();

  $this->assertDatabaseMissing('categories', [
    'id' => $category->id,
  ]);

  $this->assertDatabaseMissing('expenses', [
    'id' => $expense->id,
  ]);
});

it('paginates categories list', function () {
  $user = User::factory()->create();

  Category::factory()->count(12)->create();

  $this->actingAs($user)
    ->get('/categories')
    ->assertSuccessful()
    ->assertInertia(
      fn(AssertableInertia $page) => $page
        ->where('categories.current_page', 1)
        ->where('categories.last_page', 2)
        ->has('categories.data', 10)
    );

  $this->actingAs($user)
    ->get('/categories?page=2')
    ->assertSuccessful()
    ->assertInertia(
      fn(AssertableInertia $page) => $page
        ->where('categories.current_page', 2)
        ->has('categories.data', 2)
    );
});
