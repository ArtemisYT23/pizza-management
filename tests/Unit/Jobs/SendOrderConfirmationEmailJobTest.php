<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendOrderConfirmationEmailJob;
use App\Mail\OrderPlacedMail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendOrderConfirmationEmailJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_order_placed_mail_to_user(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $pizza = Pizza::factory()->create(['name' => 'Margherita']);
        $pizza->ingredients()->attach(
            Ingredient::factory()->count(2)->create()->pluck('id')
        );
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'pizza_id' => $pizza->id,
        ]);

        (new SendOrderConfirmationEmailJob($order->id))->handle();

        Mail::assertSent(OrderPlacedMail::class, function (OrderPlacedMail $mail) use ($user, $order) {
            return $mail->order->is($order)
                && $mail->hasTo($user->email);
        });
    }
}
