<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    protected $model = Ingredient::class;

    private static array $ingredients = [
        'Mozzarella', 'Pepperoni', 'Jamón', 'Champiñones', 'Pimiento',
        'Cebolla', 'Aceitunas', 'Tomate', 'Albahaca', 'Orégano',
        'Anchoas', 'Salchicha italiana', 'Tocino', 'Piña', 'Jalapeño',
        'Espinaca', 'Ajo', 'Ricotta', 'Parmesano', 'Gorgonzola',
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(self::$ingredients),
        ];
    }
}
