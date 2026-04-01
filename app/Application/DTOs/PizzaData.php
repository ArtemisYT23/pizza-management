<?php

namespace App\Application\DTOs;

final readonly class PizzaData
{
    /**
     * @param  list<string>  $ingredientIds  UUIDs of ingredients to attach
     */
    public function __construct(
        public string $name,
        public float $price,
        public ?string $description = null,
        public ?string $imageUrl = null,
        public array $ingredientIds = [],
    ) {}
}
