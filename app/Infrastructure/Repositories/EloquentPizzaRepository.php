<?php

namespace App\Infrastructure\Repositories;

use App\Application\DTOs\PizzaData;
use App\Domain\Contracts\PizzaRepositoryInterface;
use App\Models\Pizza;
use Illuminate\Database\Eloquent\Collection;

final class EloquentPizzaRepository implements PizzaRepositoryInterface
{
    public function allWithIngredients(): Collection
    {
        return Pizza::with('ingredients')->orderBy('name')->get();
    }

    public function findOrFail(string $id): Pizza
    {
        return Pizza::findOrFail($id);
    }

    public function findWithIngredients(string $id): Pizza
    {
        return Pizza::with('ingredients')->findOrFail($id);
    }

    public function create(PizzaData $data): Pizza
    {
        $pizza = Pizza::create([
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'image_url' => $data->imageUrl,
        ]);

        if ($data->ingredientIds !== []) {
            $pizza->ingredients()->attach($data->ingredientIds);
        }

        return $pizza->load('ingredients');
    }

    public function update(string $id, PizzaData $data): Pizza
    {
        $pizza = $this->findOrFail($id);

        $pizza->update([
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'image_url' => $data->imageUrl,
        ]);

        $pizza->ingredients()->sync($data->ingredientIds);

        return $pizza->fresh('ingredients');
    }

    public function delete(string $id): void
    {
        $this->findOrFail($id)->delete();
    }
}
