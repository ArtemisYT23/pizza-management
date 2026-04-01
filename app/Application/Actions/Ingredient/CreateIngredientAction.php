<?php

namespace App\Application\Actions\Ingredient;

use App\Application\DTOs\IngredientData;
use App\Domain\Contracts\IngredientRepositoryInterface;
use App\Models\Ingredient;

final readonly class CreateIngredientAction
{
    public function __construct(
        private IngredientRepositoryInterface $repository,
    ) {}

    public function execute(IngredientData $data): Ingredient
    {
        return $this->repository->create($data);
    }
}
