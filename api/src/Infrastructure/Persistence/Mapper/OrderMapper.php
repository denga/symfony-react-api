<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Model\OrderItem;
use App\Infrastructure\Persistence\Entity\OrderDoctrineEntity;

final class OrderMapper
{
    public static function toPersistence(Order $order): OrderDoctrineEntity
    {
        /** @var array<int, array{sku: string, quantity: int, price_cents: int}> $items */
        $items = array_map(fn (OrderItem $orderItem): array => [
            'sku' => $orderItem->sku(),
            'quantity' => $orderItem->quantity(),
            'price_cents' => $orderItem->unitPriceCents(),
        ], $order->items());

        return new OrderDoctrineEntity($order->id()->toString(), $order->customerId(), $items, $order->isPaid());
    }

    public static function toDomain(OrderDoctrineEntity $orderDoctrineEntity): Order
    {
        $items = array_map(fn (array $it): OrderItem => new OrderItem(
            $it['sku'],
            $it['quantity'],
            $it['price_cents']
        ), $orderDoctrineEntity->getItems());

        $order = new Order(OrderId::fromString($orderDoctrineEntity->getId()), $orderDoctrineEntity->getCustomerId(), array_values($items));
        if ($orderDoctrineEntity->isPaid()) {
            // markPaid uses invariants — but here we reflect persisted state
            // Option A: use reflection/private setter — simpler: set paid via internal method if allowed
            $reflectionClass = new \ReflectionClass($order);
            $prop = $reflectionClass->getProperty('paid');
            $prop->setValue($order, true);
        }

        return $order;
    }
}
