<?php

namespace App\Domain\Contracts;

use App\Application\DTOs\OrderData;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /** @return Collection<int, Order> */
    public function allWithRelations(): Collection;

    /** @return Collection<int, Order> */
    public function findByUser(string $userId): Collection;

    public function create(OrderData $data): Order;
}
