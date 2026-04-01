<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_id_is_uuid(): void
    {
        $order = Order::factory()->create();

        $this->assertTrue(Str::isUuid($order->id));
        $this->assertTrue(Str::isUuid($order->user_id));
        $this->assertTrue(Str::isUuid($order->pizza_id));
    }

    public function test_can_create_order(): void
    {
        $user = User::factory()->create();
        $pizza = Pizza::factory()->create();
        $now = now();

        $order = Order::create([
            'user_id' => $user->id,
            'pizza_id' => $pizza->id,
            'ordered_at' => $now,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'pizza_id' => $pizza->id,
        ]);
    }

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    public function test_belongs_to_pizza(): void
    {
        $pizza = Pizza::factory()->create();
        $order = Order::factory()->create(['pizza_id' => $pizza->id]);

        $this->assertInstanceOf(Pizza::class, $order->pizza);
        $this->assertEquals($pizza->id, $order->pizza->id);
    }

    public function test_ordered_at_is_cast_to_datetime(): void
    {
        $order = Order::factory()->create();

        $this->assertInstanceOf(CarbonImmutable::class, $order->ordered_at);
    }

    public function test_cascade_deletes_when_user_is_deleted(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_cascade_deletes_when_pizza_is_deleted(): void
    {
        $pizza = Pizza::factory()->create();
        $order = Order::factory()->create(['pizza_id' => $pizza->id]);

        $pizza->delete();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_user_has_many_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(5)->create(['user_id' => $user->id]);

        $this->assertCount(5, $user->orders);
    }

    public function test_factory_creates_valid_order(): void
    {
        $order = Order::factory()->create();

        $this->assertNotNull($order->user_id);
        $this->assertNotNull($order->pizza_id);
        $this->assertNotNull($order->ordered_at);
    }
}
