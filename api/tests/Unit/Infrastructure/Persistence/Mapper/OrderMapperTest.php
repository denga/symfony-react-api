<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistence\Mapper;

use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Model\OrderItem;
use App\Infrastructure\Persistence\Entity\OrderDoctrineEntity;
use App\Infrastructure\Persistence\Mapper\OrderMapper;
use PHPUnit\Framework\TestCase;

final class OrderMapperTest extends TestCase
{
    public function testToPersistenceMapsOrderToEntity(): void
    {
        $orderId = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $order = new Order($orderId, 'customer-123', [
            new OrderItem('SKU-1', 2, 500),
            new OrderItem('SKU-2', 1, 1000),
        ]);

        $orderDoctrineEntity = OrderMapper::toPersistence($order);

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $orderDoctrineEntity->getId());
        $this->assertSame('customer-123', $orderDoctrineEntity->getCustomerId());
        $this->assertFalse($orderDoctrineEntity->isPaid());
        $this->assertSame([
            [
                'sku' => 'SKU-1',
                'quantity' => 2,
                'price_cents' => 500,
            ],
            [
                'sku' => 'SKU-2',
                'quantity' => 1,
                'price_cents' => 1000,
            ],
        ], $orderDoctrineEntity->getItems());
    }

    public function testToPersistenceMapsPaidOrder(): void
    {
        $order = new Order(OrderId::generate(), 'customer-456', [new OrderItem('SKU-1', 1, 100)]);
        $order->markPaid();

        $orderDoctrineEntity = OrderMapper::toPersistence($order);

        $this->assertTrue($orderDoctrineEntity->isPaid());
    }

    public function testToDomainMapsEntityToOrder(): void
    {
        $orderDoctrineEntity = new OrderDoctrineEntity(
            '550e8400-e29b-41d4-a716-446655440000',
            'customer-123',
            [
                [
                    'sku' => 'SKU-1',
                    'quantity' => 2,
                    'price_cents' => 500,
                ],
            ],
            false
        );

        $order = OrderMapper::toDomain($orderDoctrineEntity);

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $order->id()->toString());
        $this->assertSame('customer-123', $order->customerId());
        $this->assertFalse($order->isPaid());
        $this->assertCount(1, $order->items());
        $this->assertSame('SKU-1', $order->items()[0]->sku());
        $this->assertSame(2, $order->items()[0]->quantity());
        $this->assertSame(500, $order->items()[0]->unitPriceCents());
        $this->assertSame(1000, $order->totalCents());
    }

    public function testToDomainMapsPaidEntityToPaidOrder(): void
    {
        $orderDoctrineEntity = new OrderDoctrineEntity(
            '550e8400-e29b-41d4-a716-446655440000',
            'customer-123',
            [[
                'sku' => 'SKU-1',
                'quantity' => 1,
                'price_cents' => 100,
            ]],
            true
        );

        $order = OrderMapper::toDomain($orderDoctrineEntity);

        $this->assertTrue($order->isPaid());
    }

    public function testRoundTripPreservesData(): void
    {
        $originalOrder = new Order(OrderId::fromString('550e8400-e29b-41d4-a716-446655440000'), 'customer-789', [
            new OrderItem('SKU-A', 3, 250),
        ]);
        $originalOrder->markPaid();

        $orderDoctrineEntity = OrderMapper::toPersistence($originalOrder);
        $restoredOrder = OrderMapper::toDomain($orderDoctrineEntity);

        $this->assertSame($originalOrder->id()->toString(), $restoredOrder->id()->toString());
        $this->assertSame($originalOrder->customerId(), $restoredOrder->customerId());
        $this->assertSame($originalOrder->isPaid(), $restoredOrder->isPaid());
        $this->assertSame($originalOrder->totalCents(), $restoredOrder->totalCents());
    }
}
