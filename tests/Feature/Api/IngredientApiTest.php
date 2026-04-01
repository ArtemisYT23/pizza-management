<?php

namespace Tests\Feature\Api;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_guest_cannot_access_ingredients(): void
    {
        $this->getJson('/api/ingredients')->assertUnauthorized();
    }

    public function test_non_admin_cannot_access_ingredients(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/ingredients')
            ->assertForbidden();
    }

    public function test_can_list_ingredients(): void
    {
        Ingredient::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->getJson('/api/ingredients')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_ingredient(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/ingredients', ['name' => 'Mozzarella'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Mozzarella');

        $this->assertDatabaseHas('ingredients', ['name' => 'Mozzarella']);
    }

    public function test_create_validates_required_name(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/ingredients', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_create_validates_unique_name(): void
    {
        Ingredient::factory()->create(['name' => 'Mozzarella']);

        $this->actingAs($this->admin)
            ->postJson('/api/ingredients', ['name' => 'Mozzarella'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_update_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create(['name' => 'Mozzarela']);

        $this->actingAs($this->admin)
            ->putJson("/api/ingredients/{$ingredient->id}", ['name' => 'Mozzarella'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Mozzarella');
    }

    public function test_update_allows_same_name_for_same_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create(['name' => 'Mozzarella']);

        $this->actingAs($this->admin)
            ->putJson("/api/ingredients/{$ingredient->id}", ['name' => 'Mozzarella'])
            ->assertOk();
    }

    public function test_can_delete_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create();

        $this->actingAs($this->admin)
            ->deleteJson("/api/ingredients/{$ingredient->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }

    public function test_delete_returns_404_for_invalid_id(): void
    {
        $this->actingAs($this->admin)
            ->deleteJson('/api/ingredients/non-existent-uuid')
            ->assertNotFound();
    }
}
