<?php

namespace App\Application\Actions\Order;

use App\Domain\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListOrdersAction
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    public function execute(): Collection
    {
        return $this->repository->allWithRelations();
    }
}
