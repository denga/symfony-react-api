<?php

declare(strict_types=1);

namespace App\Application\Command;

final readonly class CreateOrderCommand
{
    /**
     * @param array<int,array{sku:string,quantity:int,price_cents:int}> $items
     */
    public function __construct(
        public string $customerId,
        public array $items,
    ) {
    }
}
