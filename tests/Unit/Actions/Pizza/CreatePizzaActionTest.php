<?php

namespace Tests\Unit\Actions\Pizza;

use App\Application\Actions\Pizza\CreatePizzaAction;
use App\Application\DTOs\PizzaData;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePizzaActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_pizza_with_ingredients(): void
    {
        $ingredients = Ingredient::factory()->count(3)->create();
        $action = app(CreatePizzaAction::class);

        $pizza = $action->execute(new PizzaData(
            name: 'Margherita',
            price: 9.99,
            description: 'Clásica italiana',
            ingredientIds: $ingredients->pluck('id')->toArray(),
        ));

        $this->assertDatabaseHas('pizzas', ['name' => 'Margherita']);
        $this->assertCount(3, $pizza->ingredients);
    }

    public function test_creates_pizza_without_ingredients(): void
    {
        $action = app(CreatePizzaAction::class);

        $pizza = $action->execute(new PizzaData(
            name: 'Pizza Base',
            price: 5.99,
        ));

        $this->assertDatabaseHas('pizzas', ['name' => 'Pizza Base']);
        $this->assertCount(0, $pizza->ingredients);
    }
}
