<?php

namespace Tests\Unit\Actions\Pizza;

use App\Application\Actions\Pizza\UpdatePizzaAction;
use App\Application\DTOs\PizzaData;
use App\Models\Ingredient;
use App\Models\Pizza;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePizzaActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_pizza_and_syncs_ingredients(): void
    {
        $pizza = Pizza::factory()->create(['name' => 'Old Name']);
        $oldIngredients = Ingredient::factory()->count(2)->create();
        $pizza->ingredients()->attach($oldIngredients->pluck('id'));

        $newIngredients = Ingredient::factory()->count(3)->create();
        $action = app(UpdatePizzaAction::class);

        $updated = $action->execute($pizza->id, new PizzaData(
            name: 'New Name',
            price: 15.99,
            description: 'Updated',
            ingredientIds: $newIngredients->pluck('id')->toArray(),
        ));

        $this->assertEquals('New Name', $updated->name);
        $this->assertCount(3, $updated->ingredients);
        $this->assertEquals(
            $newIngredients->pluck('id')->sort()->values()->toArray(),
            $updated->ingredients->pluck('id')->sort()->values()->toArray(),
        );
    }
}
