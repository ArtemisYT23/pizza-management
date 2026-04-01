<?php

namespace App\Models;

use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Ingredient extends Model
{
    /** @use HasFactory<IngredientFactory> */
    use HasFactory, HasUuids;

    /**
     * @return BelongsToMany<Pizza, $this>
     */
    public function pizzas(): BelongsToMany
    {
        return $this->belongsToMany(Pizza::class)->withTimestamps();
    }
}
