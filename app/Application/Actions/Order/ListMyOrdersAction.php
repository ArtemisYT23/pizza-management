<?php

namespace App\Application\Actions\Order;

use App\Domain\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListMyOrdersAction
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    /** @return Collection<int, Order> */
    public function execute(string $userId): Collection
    {
        return $this->repository->findByUser($userId);
    }
}
