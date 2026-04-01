<?php

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PizzaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_pizza(): void
    {
        $pizza = Pizza::factory()->create([
            'name' => 'Margherita',
            'price' => 9.99,
        ]);

        $this->assertDatabaseHas('pizzas', [
            'name' => 'Margherita',
            'price' => 9.99,
        ]);
    }

    public function test_id_is_uuid(): void
    {
        $pizza = Pizza::factory()->create();

        $this->assertTrue(Str::isUuid($pizza->id));
    }

    public function test_belongs_to_many_ingredients(): void
    {
        $pizza = Pizza::factory()->create();
        $ingredients = Ingredient::factory()->count(4)->create();

        $pizza->ingredients()->attach($ingredients->pluck('id'));

        $this->assertCount(4, $pizza->ingredients);
        $this->assertInstanceOf(Ingredient::class, $pizza->ingredients->first());
    }

    public function test_has_many_orders(): void
    {
        $pizza = Pizza::factory()->create();
        $user = User::factory()->create();

        Order::factory()->count(3)->create([
            'pizza_id' => $pizza->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $pizza->orders);
        $this->assertInstanceOf(Order::class, $pizza->orders->first());
    }

    public function test_price_is_cast_to_decimal(): void
    {
        $pizza = Pizza::factory()->create(['price' => 12.5]);

        $this->assertEquals('12.50', $pizza->price);
    }

    public function test_description_is_nullable(): void
    {
        $pizza = Pizza::factory()->create(['description' => null]);

        $this->assertNull($pizza->description);
    }

    public function test_pivot_table_prevents_duplicate_ingredients(): void
    {
        $pizza = Pizza::factory()->create();
        $ingredient = Ingredient::factory()->create();

        $pizza->ingredients()->attach($ingredient->id);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $pizza->ingredients()->attach($ingredient->id);
    }
}
