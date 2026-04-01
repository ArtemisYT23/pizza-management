<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ordered_at' => $this->ordered_at,
            'user' => [
                'id' => $this->whenLoaded('user', fn () => $this->user->id),
                'name' => $this->whenLoaded('user', fn () => $this->user->name),
                'email' => $this->whenLoaded('user', fn () => $this->user->email),
            ],
            'pizza' => new PizzaResource($this->whenLoaded('pizza')),
            'created_at' => $this->created_at,
        ];
    }
}
