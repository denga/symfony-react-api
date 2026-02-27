<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Event\OrderPlaced;
use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Model\OrderItem;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function testCreatesOrderWithItems(): void
    {
        $orderId = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $items = [new OrderItem('SKU-1', 2, 500)];

        $order = new Order($orderId, 'customer-123', $items);

        $this->assertSame($orderId, $order->id());
        $this->assertSame('customer-123', $order->customerId());
        $this->assertSame($items, $order->items());
        $this->assertSame(1000, $order->totalCents());
        $this->assertFalse($order->isPaid());
    }

    public function testThrowsOnEmptyItems(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order must contain at least one item');

        new Order(OrderId::generate(), 'customer-123', []);
    }

    public function testMarkPaidReleasesOrderPlacedEvent(): void
    {
        $order = new Order(OrderId::generate(), 'customer-123', [new OrderItem('SKU-1', 1, 100)]);

        $order->markPaid();

        $this->assertTrue($order->isPaid());
        $events = $order->releaseEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(OrderPlaced::class, $events[0]);
    }

    public function testMarkPaidTwiceThrows(): void
    {
        $order = new Order(OrderId::generate(), 'customer-123', [new OrderItem('SKU-1', 1, 100)]);
        $order->markPaid();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Order already paid');

        $order->markPaid();
    }

    public function testReleaseEventsClearsEvents(): void
    {
        $order = new Order(OrderId::generate(), 'customer-123', [new OrderItem('SKU-1', 1, 100)]);
        $order->markPaid();

        $events1 = $order->releaseEvents();
        $events2 = $order->releaseEvents();

        $this->assertCount(1, $events1);
        $this->assertCount(0, $events2);
    }

    public function testFromPersistenceReconstitutesPaidOrder(): void
    {
        $orderId = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $items = [new OrderItem('SKU-1', 1, 100)];

        $order = Order::fromPersistence($orderId, 'customer-123', $items, true);

        $this->assertTrue($order->isPaid());
        $this->assertSame($orderId, $order->id());
        $this->assertSame(100, $order->totalCents());
    }

    public function testFromPersistenceReconstitutesUnpaidOrder(): void
    {
        $orderId = OrderId::generate();
        $items = [new OrderItem('SKU-1', 1, 100)];

        $order = Order::fromPersistence($orderId, 'customer-123', $items, false);

        $this->assertFalse($order->isPaid());
    }
}
