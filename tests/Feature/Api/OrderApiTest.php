<?php

namespace Tests\Feature\Api;

use App\Jobs\SendOrderConfirmationEmailJob;
use App\Mail\OrderPlacedMail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_list_orders(): void
    {
        $this->getJson('/api/orders')->assertUnauthorized();
    }

    public function test_guest_cannot_place_order(): void
    {
        $this->postJson('/api/orders', [])->assertUnauthorized();
    }

    public function test_admin_can_list_all_orders(): void
    {
        Order::factory()->count(3)->create();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_only_lists_own_orders(): void
    {
        $other = User::factory()->create();
        Order::factory()->count(2)->create(['user_id' => $this->user->id]);
        Order::factory()->create(['user_id' => $other->id]);

        $this->actingAs($this->user)
            ->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_orders_include_pizza(): void
    {
        Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/orders')
            ->assertOk();

        $order = $response->json('data.0');
        $this->assertArrayHasKey('pizza', $order);
        $this->assertArrayNotHasKey('user', $order);
    }

    public function test_admin_orders_include_user_and_pizza(): void
    {
        Order::factory()->create(['user_id' => $this->user->id]);

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->getJson('/api/orders')
            ->assertOk();

        $order = $response->json('data.0');
        $this->assertArrayHasKey('user', $order);
        $this->assertArrayHasKey('pizza', $order);
    }

    public function test_can_place_order(): void
    {
        Mail::fake();

        $pizza = Pizza::factory()->create();
        $pizza->ingredients()->attach(
            Ingredient::factory()->count(2)->create()->pluck('id')
        );

        $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'pizza_id' => $pizza->id,
            ])
            ->assertCreated()
            ->assertJsonPath('data.user.id', $this->user->id)
            ->assertJsonPath('data.pizza.id', $pizza->id);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'pizza_id' => $pizza->id,
        ]);

        $user = $this->user;
        Mail::assertSent(OrderPlacedMail::class, function (OrderPlacedMail $mail) use ($pizza, $user) {
            return $mail->order->pizza_id === $pizza->id
                && $mail->hasTo($user->email);
        });
    }

    public function test_placing_order_dispatches_confirmation_email_job(): void
    {
        Queue::fake();

        $pizza = Pizza::factory()->create();
        $user = $this->user;

        $this->actingAs($this->user)
            ->postJson('/api/orders', ['pizza_id' => $pizza->id])
            ->assertCreated();

        Queue::assertPushed(SendOrderConfirmationEmailJob::class, 1);
        Queue::assertPushed(SendOrderConfirmationEmailJob::class, function (SendOrderConfirmationEmailJob $job) use ($user) {
            return Order::where('id', $job->orderId)->where('user_id', $user->id)->exists();
        });
    }

    public function test_place_order_validates_pizza_exists(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/orders', [
                'pizza_id' => 'non-existent-uuid',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['pizza_id']);
    }

    public function test_place_order_requires_pizza_id(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/orders', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['pizza_id']);
    }
}
