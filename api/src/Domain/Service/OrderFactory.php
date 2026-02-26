<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Model\OrderItem;

final class OrderFactory
{
    /**
     * @param array<int,array{sku:string,quantity:int,price_cents:int}> $itemsData
     */
    public function createNew(string $customerId, array $itemsData): Order
    {
        $items = array_map(fn (array $it): OrderItem => new OrderItem($it['sku'], $it['quantity'], $it['price_cents']), $itemsData);

        return new Order(OrderId::generate(), $customerId, array_values($items));
    }
}
