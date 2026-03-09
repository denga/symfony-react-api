<?php

declare(strict_types=1);

namespace App\Shared\Telemetry;

interface OrderMetricsInterface
{
    public function recordOrderCreated(int $itemsCount, int $totalCents, int $itemsQuantity): void;
}
