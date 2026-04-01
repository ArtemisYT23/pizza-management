<?php

namespace App\Domain\Contracts;

use App\Application\DTOs\IngredientData;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;

interface IngredientRepositoryInterface
{
    /** @return Collection<int, Ingredient> */
    public function all(): Collection;

    public function findOrFail(string $id): Ingredient;

    public function create(IngredientData $data): Ingredient;

    public function update(string $id, IngredientData $data): Ingredient;

    public function delete(string $id): void;
}
