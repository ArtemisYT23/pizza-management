<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Reintentos ante fallos de red transitorios hacia la API de Brevo.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * @var list<int>
     */
    public $backoff = [10, 30];

    public function __construct(
        public string $orderId,
    ) {}

    public function handle(): void
    {
        $order = Order::with(['user', 'pizza.ingredients'])->findOrFail($this->orderId);

        Mail::to($order->user->email)->send(new OrderPlacedMail($order));
    }

    public function failed(Throwable $exception): void
    {
        Log::error('SendOrderConfirmationEmailJob failed', [
            'order_id' => $this->orderId,
            'message' => $exception->getMessage(),
            'exception' => $exception,
        ]);
    }
}
