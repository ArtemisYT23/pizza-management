<?php

namespace App\Application\Actions\Order;

use App\Application\DTOs\OrderData;
use App\Domain\Contracts\OrderRepositoryInterface;
use App\Domain\Contracts\PizzaRepositoryInterface;
use App\Events\OrderPlaced;
use App\Models\Order;

final readonly class PlaceOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PizzaRepositoryInterface $pizzaRepository,
    ) {}

    public function execute(OrderData $data): Order
    {
        $this->pizzaRepository->findOrFail($data->pizzaId);

        $order = $this->orderRepository->create($data);

        event(new OrderPlaced($order));

        return $order;
    }
}
