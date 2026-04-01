<?php

namespace App\Domain\Contracts;

use App\Application\DTOs\PizzaData;
use App\Models\Pizza;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PizzaRepositoryInterface
{
    /** @return Collection<int, Pizza> */
    public function allWithIngredients(): Collection;

    public function paginateWithIngredients(int $perPage, int $page): LengthAwarePaginator;

    public function findOrFail(string $id): Pizza;

    public function findWithIngredients(string $id): Pizza;

    public function create(PizzaData $data): Pizza;

    public function update(string $id, PizzaData $data): Pizza;

    public function delete(string $id): void;
}
