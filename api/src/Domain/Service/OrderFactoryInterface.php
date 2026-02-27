<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Order;

interface OrderFactoryInterface
{
    /**
     * @param array<int, array{sku: string, quantity: int, price_cents: int}> $itemsData
     */
    public function createNew(string $customerId, array $itemsData): Order;
}
