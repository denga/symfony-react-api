<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Telemetry;

use App\Domain\Event\OrderCreated;
use App\Infrastructure\Telemetry\OrderMetricsSubscriber;
use App\Shared\Telemetry\OrderMetricsInterface;
use PHPUnit\Framework\TestCase;

final class OrderMetricsSubscriberTest extends TestCase
{
    public function testSubscribesToOrderCreatedEvent(): void
    {
        $subscribedEvents = OrderMetricsSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(OrderCreated::class, $subscribedEvents);
        $this->assertSame('onOrderCreated', $subscribedEvents[OrderCreated::class]);
    }

    public function testOnOrderCreatedRecordsMetrics(): void
    {
        $mockMetrics = $this->createMock(OrderMetricsInterface::class);
        $mockMetrics->expects($this->once())
            ->method('recordOrderCreated')
            ->with(3, 15000, 7);

        $orderMetricsSubscriber = new OrderMetricsSubscriber($mockMetrics);

        $orderMetricsSubscriber->onOrderCreated(new OrderCreated(
            'order-123',
            3,
            15000,
            7,
        ));
    }
}
