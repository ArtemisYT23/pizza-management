<?php

namespace App\Application\Actions\Ingredient;

use App\Domain\Contracts\IngredientRepositoryInterface;

final readonly class DeleteIngredientAction
{
    public function __construct(
        private IngredientRepositoryInterface $repository,
    ) {}

    public function execute(string $id): void
    {
        $this->repository->delete($id);
    }
}
