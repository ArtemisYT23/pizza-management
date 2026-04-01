<?php

namespace App\Application\Actions\Pizza;

use App\Application\DTOs\PizzaData;
use App\Domain\Contracts\PizzaRepositoryInterface;
use App\Models\Pizza;

final readonly class UpdatePizzaAction
{
    public function __construct(
        private PizzaRepositoryInterface $repository,
    ) {}

    public function execute(string $id, PizzaData $data): Pizza
    {
        return $this->repository->update($id, $data);
    }
}
