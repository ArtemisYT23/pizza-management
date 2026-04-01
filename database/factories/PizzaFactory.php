<?php

namespace Database\Factories;

use App\Models\Pizza;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pizza>
 */
class PizzaFactory extends Factory
{
    protected $model = Pizza::class;

    private static array $pizzas = [
        'Margherita' => 'Clásica con tomate, mozzarella y albahaca',
        'Pepperoni' => 'Abundante pepperoni con mozzarella derretida',
        'Hawaiana' => 'Jamón y piña sobre base de queso',
        'Cuatro Quesos' => 'Mozzarella, gorgonzola, parmesano y ricotta',
        'Vegetariana' => 'Champiñones, pimiento, cebolla y aceitunas',
        'Napolitana' => 'Anchoas, tomate, aceitunas y orégano',
        'BBQ Chicken' => 'Pollo a la barbacoa con cebolla y tocino',
        'Diavola' => 'Salchicha italiana picante con jalapeños',
    ];

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(array_keys(self::$pizzas));

        return [
            'name' => $name,
            'description' => self::$pizzas[$name],
            'price' => fake()->randomFloat(2, 8.99, 24.99),
            'image_url' => null,
        ];
    }
}
