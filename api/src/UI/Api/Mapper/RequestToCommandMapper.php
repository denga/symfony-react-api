<?php

declare(strict_types=1);

namespace App\UI\Api\Mapper;

use App\Application\Command\CreateOrderCommand;
use App\UI\Api\Request\CreateOrderRequest;

final class RequestToCommandMapper
{
    public function map(CreateOrderRequest $createOrderRequest): CreateOrderCommand
    {
        // Defensive mapping / normalisierung
        $items = array_map(fn (array $it): array => [
            'sku' => $it['sku'],
            'quantity' => $it['quantity'],
            'price_cents' => $it['price_cents'],
        ], $createOrderRequest->items);

        return new CreateOrderCommand((string) $createOrderRequest->customerId, $items);
    }
}
