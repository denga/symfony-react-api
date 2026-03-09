<?php

declare(strict_types=1);

namespace App\Infrastructure\Telemetry;

use App\Domain\Event\OrderCreated;
use App\Shared\Telemetry\OrderMetricsInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class OrderMetricsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private OrderMetricsInterface $orderMetrics,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderCreated::class => 'onOrderCreated',
        ];
    }

    public function onOrderCreated(OrderCreated $orderCreated): void
    {
        $this->orderMetrics->recordOrderCreated(
            $orderCreated->itemsCount,
            $orderCreated->totalCents,
            $orderCreated->itemsQuantity,
        );
    }
}
