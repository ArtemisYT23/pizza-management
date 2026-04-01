<?php

namespace App\Application\DTOs;

final readonly class OrderData
{
    public function __construct(
        public string $userId,
        public string $pizzaId,
    ) {}
}
