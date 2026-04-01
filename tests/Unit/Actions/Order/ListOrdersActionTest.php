<?php

namespace Tests\Unit\Actions\Order;

use App\Application\Actions\Order\ListOrdersAction;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListOrdersActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_orders_with_relations(): void
    {
        Order::factory()->count(3)->create();

        $action = app(ListOrdersAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        $this->assertTrue($result->first()->relationLoaded('user'));
        $this->assertTrue($result->first()->relationLoaded('pizza'));
    }

    public function test_orders_are_sorted_by_most_recent(): void
    {
        $old = Order::factory()->create(['ordered_at' => now()->subDays(5)]);
        $recent = Order::factory()->create(['ordered_at' => now()]);

        $action = app(ListOrdersAction::class);
        $result = $action->execute();

        $this->assertEquals($recent->id, $result->first()->id);
        $this->assertEquals($old->id, $result->last()->id);
    }
}
