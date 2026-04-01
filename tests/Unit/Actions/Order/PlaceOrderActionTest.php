<?php

namespace Tests\Unit\Actions\Order;

use App\Application\Actions\Order\PlaceOrderAction;
use App\Application\DTOs\OrderData;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceOrderActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_places_order_successfully(): void
    {
        $user = User::factory()->create();
        $pizza = Pizza::factory()->create();

        $action = app(PlaceOrderAction::class);
        $order = $action->execute(new OrderData(
            userId: $user->id,
            pizzaId: $pizza->id,
        ));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'pizza_id' => $pizza->id,
        ]);
        $this->assertNotNull($order->ordered_at);
        $this->assertTrue($order->relationLoaded('user'));
        $this->assertTrue($order->relationLoaded('pizza'));
    }

    public function test_fails_with_invalid_pizza(): void
    {
        $user = User::factory()->create();

        $action = app(PlaceOrderAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute(new OrderData(
            userId: $user->id,
            pizzaId: 'non-existent-uuid',
        ));
    }

    public function test_order_has_pizza_with_ingredients(): void
    {
        $user = User::factory()->create();
        $pizza = Pizza::factory()->create();
        $pizza->ingredients()->attach(
            \App\Models\Ingredient::factory()->count(3)->create()->pluck('id')
        );

        $action = app(PlaceOrderAction::class);
        $order = $action->execute(new OrderData(
            userId: $user->id,
            pizzaId: $pizza->id,
        ));

        $this->assertCount(3, $order->pizza->ingredients);
    }
}
