<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class OrderSummary
{
    public function __construct(
        public string $orderId,
        public string $customerId,
        public int $totalCents,
        public bool $paid,
    ) {
    }
}
