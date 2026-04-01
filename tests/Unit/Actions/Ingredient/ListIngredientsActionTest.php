<?php

namespace Tests\Unit\Actions\Ingredient;

use App\Application\Actions\Ingredient\ListIngredientsAction;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListIngredientsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_all_ingredients_sorted_by_name(): void
    {
        Ingredient::factory()->create(['name' => 'Tocino']);
        Ingredient::factory()->create(['name' => 'Albahaca']);
        Ingredient::factory()->create(['name' => 'Mozzarella']);

        $action = app(ListIngredientsAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        $this->assertEquals(['Albahaca', 'Mozzarella', 'Tocino'], $result->pluck('name')->toArray());
    }

    public function test_returns_empty_collection_when_no_ingredients(): void
    {
        $action = app(ListIngredientsAction::class);

        $this->assertCount(0, $action->execute());
    }
}
