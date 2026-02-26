<?php

declare(strict_types=1);

namespace App\Domain\Event;

final readonly class OrderPlaced
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
