<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $orderId,
    ) {}

    public function handle(): void
    {
        $order = Order::with(['user', 'pizza.ingredients'])->findOrFail($this->orderId);

        Mail::to($order->user->email)->send(new OrderPlacedMail($order));
    }
}
