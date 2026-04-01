<?php

namespace App\Application\Actions\Ingredient;

use App\Application\DTOs\IngredientData;
use App\Domain\Contracts\IngredientRepositoryInterface;
use App\Models\Ingredient;

final readonly class UpdateIngredientAction
{
    public function __construct(
        private IngredientRepositoryInterface $repository,
    ) {}

    public function execute(string $id, IngredientData $data): Ingredient
    {
        return $this->repository->update($id, $data);
    }
}
