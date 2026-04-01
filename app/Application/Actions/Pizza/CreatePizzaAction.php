<?php

namespace App\Application\Actions\Pizza;

use App\Application\DTOs\PizzaData;
use App\Domain\Contracts\PizzaRepositoryInterface;
use App\Models\Pizza;

final readonly class CreatePizzaAction
{
    public function __construct(
        private PizzaRepositoryInterface $repository,
    ) {}

    public function execute(PizzaData $data): Pizza
    {
        return $this->repository->create($data);
    }
}
