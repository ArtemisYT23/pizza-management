<?php

namespace App\Application\Actions\Ingredient;

use App\Domain\Contracts\IngredientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListIngredientsAction
{
    public function __construct(
        private IngredientRepositoryInterface $repository,
    ) {}

    public function execute(): Collection
    {
        return $this->repository->all();
    }
}
