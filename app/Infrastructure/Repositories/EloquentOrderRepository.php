<?php

namespace App\Infrastructure\Repositories;

use App\Application\DTOs\OrderData;
use App\Domain\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function allWithRelations(): Collection
    {
        return Order::with(['user', 'pizza.ingredients'])
            ->orderByDesc('ordered_at')
            ->get();
    }

    public function findByUser(string $userId): Collection
    {
        return Order::with('pizza.ingredients')
            ->where('user_id', $userId)
            ->orderByDesc('ordered_at')
            ->get();
    }

    public function create(OrderData $data): Order
    {
        $order = Order::create([
            'user_id' => $data->userId,
            'pizza_id' => $data->pizzaId,
            'ordered_at' => now(),
        ]);

        return $order->load(['user', 'pizza.ingredients']);
    }
}
