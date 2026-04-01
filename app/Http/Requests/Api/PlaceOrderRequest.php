<?php

namespace App\Http\Requests\Api;

use App\Application\DTOs\OrderData;
use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'pizza_id' => ['required', 'uuid', 'exists:pizzas,id'],
        ];
    }

    public function toDto(): OrderData
    {
        return new OrderData(
            userId: $this->user()->id,
            pizzaId: $this->validated('pizza_id'),
        );
    }
}
