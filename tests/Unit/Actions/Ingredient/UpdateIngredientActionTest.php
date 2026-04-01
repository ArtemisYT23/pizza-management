<?php

namespace Tests\Unit\Actions\Ingredient;

use App\Application\Actions\Ingredient\UpdateIngredientAction;
use App\Application\DTOs\IngredientData;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateIngredientActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create(['name' => 'Mozzarela']);
        $action = app(UpdateIngredientAction::class);

        $updated = $action->execute($ingredient->id, new IngredientData(name: 'Mozzarella'));

        $this->assertEquals('Mozzarella', $updated->name);
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'name' => 'Mozzarella']);
    }

    public function test_fails_with_invalid_id(): void
    {
        $action = app(UpdateIngredientAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute('non-existent-uuid', new IngredientData(name: 'Test'));
    }
}
