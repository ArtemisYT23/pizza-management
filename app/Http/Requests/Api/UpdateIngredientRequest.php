<?php

namespace App\Http\Requests\Api;

use App\Application\DTOs\IngredientData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ingredients', 'name')->ignore($this->route('ingredient')),
            ],
        ];
    }

    public function toDto(): IngredientData
    {
        return new IngredientData(
            name: $this->validated('name'),
        );
    }
}
