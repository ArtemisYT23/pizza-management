<?php

namespace App\Application\Actions\Pizza;

use App\Domain\Contracts\PizzaRepositoryInterface;

final readonly class DeletePizzaAction
{
    public function __construct(
        private PizzaRepositoryInterface $repository,
    ) {}

    public function execute(string $id): void
    {
        $this->repository->delete($id);
    }
}
