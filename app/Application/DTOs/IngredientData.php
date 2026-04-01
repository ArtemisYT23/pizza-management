<?php

namespace App\Application\DTOs;

final readonly class IngredientData
{
    public function __construct(
        public string $name,
    ) {}
}
