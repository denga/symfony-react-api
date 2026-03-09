<?php

declare(strict_types=1);

namespace App\Domain\Event;

final readonly class OrderCreated
{
    public function __construct(
        public string $orderId,
        public int $itemsCount,
        public int $totalCents,
        public int $itemsQuantity,
    ) {
    }
}
