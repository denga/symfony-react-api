<?php

declare(strict_types=1);

namespace App\Infrastructure\Telemetry;

use App\Shared\Telemetry\OrderMetricsInterface;
use OpenTelemetry\API\Metrics\CounterInterface;
use OpenTelemetry\API\Metrics\HistogramInterface;
use OpenTelemetry\API\Metrics\MeterProviderInterface;

final readonly class OpenTelemetryOrderMetrics implements OrderMetricsInterface
{
    private CounterInterface $ordersCreatedCounter;
    private HistogramInterface $itemsCountHistogram;
    private HistogramInterface $totalCentsHistogram;
    private HistogramInterface $itemsQuantityHistogram;

    public function __construct(MeterProviderInterface $meterProvider)
    {
        $meter = $meterProvider->getMeter('order-api');

        $this->ordersCreatedCounter = $meter->createCounter(
            'order.created',
            '{order}',
            'Total number of successfully created orders',
        );

        $this->itemsCountHistogram = $meter->createHistogram(
            'order.items_count',
            '{item}',
            'Number of distinct line items per order',
            [
                'ExplicitBucketBoundaries' => [1, 2, 3, 5, 10, 20, 50],
            ],
        );

        $this->totalCentsHistogram = $meter->createHistogram(
            'order.total_cents',
            'ct',
            'Total order value in cents',
            [
                'ExplicitBucketBoundaries' => [500, 1000, 2500, 5000, 10000, 25000, 50000, 100000],
            ],
        );

        $this->itemsQuantityHistogram = $meter->createHistogram(
            'order.items_quantity',
            '{item}',
            'Sum of all item quantities per order',
            [
                'ExplicitBucketBoundaries' => [1, 2, 5, 10, 25, 50, 100],
            ],
        );
    }

    public function recordOrderCreated(int $itemsCount, int $totalCents, int $itemsQuantity): void
    {
        $this->ordersCreatedCounter->add(1);
        $this->itemsCountHistogram->record($itemsCount);
        $this->totalCentsHistogram->record($totalCents);
        $this->itemsQuantityHistogram->record($itemsQuantity);
    }
}
