<?php

namespace App\Infrastructure\Repositories;

use App\Application\DTOs\IngredientData;
use App\Domain\Contracts\IngredientRepositoryInterface;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;

final class EloquentIngredientRepository implements IngredientRepositoryInterface
{
    public function all(): Collection
    {
        return Ingredient::orderBy('name')->get();
    }

    public function findOrFail(string $id): Ingredient
    {
        return Ingredient::findOrFail($id);
    }

    public function create(IngredientData $data): Ingredient
    {
        return Ingredient::create([
            'name' => $data->name,
        ]);
    }

    public function update(string $id, IngredientData $data): Ingredient
    {
        $ingredient = $this->findOrFail($id);

        $ingredient->update([
            'name' => $data->name,
        ]);

        return $ingredient->fresh();
    }

    public function delete(string $id): void
    {
        $this->findOrFail($id)->delete();
    }
}
