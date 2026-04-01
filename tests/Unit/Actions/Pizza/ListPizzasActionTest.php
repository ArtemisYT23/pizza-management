<?php

namespace Tests\Unit\Actions\Pizza;

use App\Application\Actions\Pizza\ListPizzasAction;
use App\Models\Ingredient;
use App\Models\Pizza;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPizzasActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_pizzas_with_ingredients(): void
    {
        $pizza = Pizza::factory()->create();
        $ingredients = Ingredient::factory()->count(2)->create();
        $pizza->ingredients()->attach($ingredients->pluck('id'));

        $action = app(ListPizzasAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->relationLoaded('ingredients'));
        $this->assertCount(2, $result->first()->ingredients);
    }
}
