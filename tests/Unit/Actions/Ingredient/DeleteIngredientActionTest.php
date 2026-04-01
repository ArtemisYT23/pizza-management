<?php

namespace Tests\Unit\Actions\Ingredient;

use App\Application\Actions\Ingredient\DeleteIngredientAction;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteIngredientActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_ingredient(): void
    {
        $ingredient = Ingredient::factory()->create();
        $action = app(DeleteIngredientAction::class);

        $action->execute($ingredient->id);

        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }

    public function test_fails_with_invalid_id(): void
    {
        $action = app(DeleteIngredientAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute('non-existent-uuid');
    }
}
