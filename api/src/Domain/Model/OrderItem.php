<?php

declare(strict_types=1);

namespace App\Domain\Model;

final readonly class OrderItem
{
    private int $quantity;
    private int $unitPriceCents;

    public function __construct(
        private string $sku,
        int $quantity,
        int $unitPriceCents,
    ) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be > 0');
        }
        if ($unitPriceCents < 0) {
            throw new \InvalidArgumentException('Price must be >= 0');
        }
        $this->quantity = $quantity;
        $this->unitPriceCents = $unitPriceCents;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPriceCents(): int
    {
        return $this->unitPriceCents;
    }

    public function totalCents(): int
    {
        return $this->quantity * $this->unitPriceCents;
    }
}
