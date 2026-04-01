<?php

namespace Tests\Unit\Actions\Ingredient;

use App\Application\Actions\Ingredient\CreateIngredientAction;
use App\Application\DTOs\IngredientData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateIngredientActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_ingredient(): void
    {
        $action = app(CreateIngredientAction::class);

        $ingredient = $action->execute(new IngredientData(name: 'Mozzarella'));

        $this->assertDatabaseHas('ingredients', ['name' => 'Mozzarella']);
        $this->assertEquals('Mozzarella', $ingredient->name);
    }

    public function test_fails_with_duplicate_name(): void
    {
        $action = app(CreateIngredientAction::class);

        $action->execute(new IngredientData(name: 'Mozzarella'));

        $this->expectException(\Illuminate\Database\QueryException::class);
        $action->execute(new IngredientData(name: 'Mozzarella'));
    }
}
