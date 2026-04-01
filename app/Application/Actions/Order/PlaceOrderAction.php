<?php

namespace App\Application\Actions\Order;

use App\Application\DTOs\OrderData;
use App\Domain\Contracts\OrderRepositoryInterface;
use App\Domain\Contracts\PizzaRepositoryInterface;
use App\Models\Order;

final readonly class PlaceOrderAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PizzaRepositoryInterface $pizzaRepository,
    ) {}

    public function execute(OrderData $data): Order
    {
        // Validate the pizza exists before placing the order
        $this->pizzaRepository->findOrFail($data->pizzaId);

        $order = $this->orderRepository->create($data);

        // TODO: dispatch OrderPlaced event here (next step)

        return $order;
    }
}
