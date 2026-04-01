<?php

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use App\Models\Pizza;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create(['name' => 'Mozzarella']);

        $this->assertDatabaseHas('ingredients', ['name' => 'Mozzarella']);
        $this->assertEquals('Mozzarella', $ingredient->name);
    }

    public function test_id_is_uuid(): void
    {
        $ingredient = Ingredient::factory()->create();

        $this->assertTrue(Str::isUuid($ingredient->id));
    }

    public function test_name_is_unique(): void
    {
        Ingredient::factory()->create(['name' => 'Mozzarella']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Ingredient::factory()->create(['name' => 'Mozzarella']);
    }

    public function test_belongs_to_many_pizzas(): void
    {
        $ingredient = Ingredient::factory()->create(['name' => 'Mozzarella']);
        $pizzas = Pizza::factory()->count(3)->create();

        $ingredient->pizzas()->attach($pizzas->pluck('id'));

        $this->assertCount(3, $ingredient->pizzas);
        $this->assertInstanceOf(Pizza::class, $ingredient->pizzas->first());
    }

    public function test_fillable_attributes(): void
    {
        $ingredient = Ingredient::create(['name' => 'Albahaca']);

        $this->assertEquals('Albahaca', $ingredient->name);
    }
}
