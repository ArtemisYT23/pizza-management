<?php

namespace App\Http\Requests\Api;

use App\Application\DTOs\PizzaData;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePizzaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'image_url' => ['nullable', 'string', 'url', 'max:2048'],
            'ingredient_ids' => ['sometimes', 'array'],
            'ingredient_ids.*' => ['required', 'uuid', 'exists:ingredients,id'],
        ];
    }

    public function toDto(): PizzaData
    {
        $validated = $this->validated();

        return new PizzaData(
            name: $validated['name'],
            price: (float) $validated['price'],
            description: $validated['description'] ?? null,
            imageUrl: $validated['image_url'] ?? null,
            ingredientIds: $validated['ingredient_ids'] ?? [],
        );
    }
}
