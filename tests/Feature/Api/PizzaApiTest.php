<?php

namespace Tests\Feature\Api;

use App\Models\Ingredient;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PizzaApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->admin()->create();
    }

    // --- Public endpoints ---

    public function test_guest_can_list_pizzas(): void
    {
        Pizza::factory()->count(3)->create();

        $this->getJson('/api/pizzas')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_list_pizzas_includes_ingredients(): void
    {
        $pizza = Pizza::factory()->create();
        $pizza->ingredients()->attach(
            Ingredient::factory()->count(2)->create()->pluck('id')
        );

        $response = $this->getJson('/api/pizzas')
            ->assertOk();

        $this->assertCount(2, $response->json('data.0.ingredients'));
    }

    public function test_guest_can_show_pizza(): void
    {
        $pizza = Pizza::factory()->create();
        $pizza->ingredients()->attach(
            Ingredient::factory()->count(3)->create()->pluck('id')
        );

        $this->getJson("/api/pizzas/{$pizza->id}")
            ->assertOk()
            ->assertJsonPath('data.name', $pizza->name)
            ->assertJsonCount(3, 'data.ingredients');
    }

    public function test_show_returns_404_for_invalid_id(): void
    {
        $this->getJson('/api/pizzas/non-existent-uuid')
            ->assertNotFound();
    }

    // --- Authenticated endpoints ---

    public function test_guest_cannot_create_pizza(): void
    {
        $this->postJson('/api/pizzas', [])->assertUnauthorized();
    }

    public function test_non_admin_cannot_create_pizza(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/pizzas', [
                'name' => 'X',
                'price' => 10,
                'ingredient_ids' => [],
            ])
            ->assertForbidden();
    }

    public function test_can_create_pizza_with_ingredients(): void
    {
        $ingredients = Ingredient::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->postJson('/api/pizzas', [
                'name' => 'Margherita',
                'description' => 'Clásica',
                'price' => 9.99,
                'ingredient_ids' => $ingredients->pluck('id')->toArray(),
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Margherita')
            ->assertJsonCount(3, 'data.ingredients');
    }

    public function test_create_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/pizzas', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_create_validates_ingredient_ids_exist(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/pizzas', [
                'name' => 'Test',
                'price' => 10,
                'ingredient_ids' => ['non-existent-uuid'],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ingredient_ids.0']);
    }

    public function test_can_update_pizza(): void
    {
        $pizza = Pizza::factory()->create(['name' => 'Old']);
        $newIngredients = Ingredient::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->putJson("/api/pizzas/{$pizza->id}", [
                'name' => 'Updated',
                'price' => 15.99,
                'ingredient_ids' => $newIngredients->pluck('id')->toArray(),
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated')
            ->assertJsonCount(2, 'data.ingredients');
    }

    public function test_can_delete_pizza(): void
    {
        $pizza = Pizza::factory()->create();

        $this->actingAs($this->admin)
            ->deleteJson("/api/pizzas/{$pizza->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('pizzas', ['id' => $pizza->id]);
    }
}
