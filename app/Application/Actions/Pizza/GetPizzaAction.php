<?php

namespace App\Application\Actions\Pizza;

use App\Domain\Contracts\PizzaRepositoryInterface;
use App\Models\Pizza;

final readonly class GetPizzaAction
{
    public function __construct(
        private PizzaRepositoryInterface $repository,
    ) {}

    public function execute(string $id): Pizza
    {
        return $this->repository->findWithIngredients($id);
    }
}
