<?php

namespace App\Http\Requests\Api;

use App\Application\DTOs\IngredientData;
use Illuminate\Foundation\Http\FormRequest;

class StoreIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:ingredients,name'],
        ];
    }

    public function toDto(): IngredientData
    {
        return new IngredientData(
            name: $this->validated('name'),
        );
    }
}
