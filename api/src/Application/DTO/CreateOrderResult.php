<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class CreateOrderResult
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
