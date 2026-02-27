<?php

declare(strict_types=1);

namespace App\UI\Api\Mapper;

use App\Application\DTO\OrderSummary;

final class OrderSummaryToResponseMapper
{
    /**
     * @param OrderSummary[] $summaries
     *
     * @return array<int, array{orderId: string, customerId: string, totalCents: int, paid: bool}>
     */
    public function map(array $summaries): array
    {
        return array_values(array_map(
            fn (OrderSummary $orderSummary): array => [
                'orderId' => $orderSummary->orderId,
                'customerId' => $orderSummary->customerId,
                'totalCents' => $orderSummary->totalCents,
                'paid' => $orderSummary->paid,
            ],
            $summaries
        ));
    }
}
