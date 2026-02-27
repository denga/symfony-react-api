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
            fn (OrderSummary $s): array => [
                'orderId' => $s->orderId,
                'customerId' => $s->customerId,
                'totalCents' => $s->totalCents,
                'paid' => $s->paid,
            ],
            $summaries
        ));
    }
}
