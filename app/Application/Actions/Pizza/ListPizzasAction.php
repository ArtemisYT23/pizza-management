<?php

namespace App\Application\Actions\Pizza;

use App\Domain\Contracts\PizzaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListPizzasAction
{
    public function __construct(
        private PizzaRepositoryInterface $repository,
    ) {}

    public function execute(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->repository->paginateWithIngredients($perPage, $page);
    }
}
