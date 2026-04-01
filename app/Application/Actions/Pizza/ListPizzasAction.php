<?php

namespace App\Application\Actions\Pizza;

use App\Domain\Contracts\PizzaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListPizzasAction
{
    public function __construct(
        private PizzaRepositoryInterface $repository,
    ) {}

    public function execute(): Collection
    {
        return $this->repository->allWithIngredients();
    }
}
