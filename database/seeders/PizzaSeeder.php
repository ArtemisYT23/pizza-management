<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Pizza;
use Illuminate\Database\Seeder;

class PizzaSeeder extends Seeder
{
    /**
     * Pizzas con sus ingredientes predefinidos.
     *
     * @var array<string, array{description: string, price: float, ingredients: list<string>}>
     */
    private array $pizzas = [
        'Margherita' => [
            'description' => 'Clásica con tomate, mozzarella fresca y albahaca',
            'price' => 9.99,
            'ingredients' => ['Mozzarella', 'Tomate', 'Albahaca', 'Orégano'],
        ],
        'Pepperoni' => [
            'description' => 'Abundante pepperoni con mozzarella derretida',
            'price' => 12.99,
            'ingredients' => ['Mozzarella', 'Pepperoni', 'Orégano'],
        ],
        'Hawaiana' => [
            'description' => 'Jamón y piña sobre base de queso mozzarella',
            'price' => 13.49,
            'ingredients' => ['Mozzarella', 'Jamón', 'Piña'],
        ],
        'Cuatro Quesos' => [
            'description' => 'Mezcla de mozzarella, gorgonzola, parmesano y ricotta',
            'price' => 14.99,
            'ingredients' => ['Mozzarella', 'Gorgonzola', 'Parmesano', 'Ricotta'],
        ],
        'Vegetariana' => [
            'description' => 'Champiñones, pimiento, cebolla y aceitunas negras',
            'price' => 11.99,
            'ingredients' => ['Mozzarella', 'Champiñones', 'Pimiento', 'Cebolla', 'Aceitunas'],
        ],
        'Napolitana' => [
            'description' => 'Anchoas, tomate fresco, aceitunas y orégano',
            'price' => 13.99,
            'ingredients' => ['Mozzarella', 'Anchoas', 'Tomate', 'Aceitunas', 'Orégano'],
        ],
        'Diavola' => [
            'description' => 'Salchicha italiana picante con jalapeños y ajo',
            'price' => 14.49,
            'ingredients' => ['Mozzarella', 'Salchicha italiana', 'Jalapeño', 'Ajo'],
        ],
        'Carbonara' => [
            'description' => 'Tocino crujiente, huevo y parmesano sobre base cremosa',
            'price' => 15.49,
            'ingredients' => ['Mozzarella', 'Tocino', 'Parmesano', 'Cebolla'],
        ],
    ];

    public function run(): void
    {
        $ingredientMap = collect($this->pizzas)
            ->flatMap(fn (array $data) => $data['ingredients'])
            ->unique()
            ->mapWithKeys(fn (string $name) => [
                $name => Ingredient::firstOrCreate(['name' => $name]),
            ]);

        foreach ($this->pizzas as $name => $data) {
            $pizza = Pizza::create([
                'name' => $name,
                'description' => $data['description'],
                'price' => $data['price'],
            ]);

            $ingredientIds = collect($data['ingredients'])
                ->map(fn (string $name) => $ingredientMap[$name]->id);

            $pizza->ingredients()->attach($ingredientIds);
        }
    }
}
