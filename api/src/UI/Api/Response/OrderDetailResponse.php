<?php

declare(strict_types=1);

namespace App\UI\Api\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'OrderDetailResponse',
    description: 'Response schema for order details',
    required: ['orderId', 'customerId', 'totalCents', 'paid'],
)]
final class OrderDetailResponse implements \JsonSerializable
{
    public function __construct(
        public string $orderId,
        public string $customerId,
        public int $totalCents,
        public bool $paid,
    ) {
    }

    /**
     * @return array{orderId: string, customerId: string, totalCents: int, paid: bool}
     */
    public function jsonSerialize(): array
    {
        return [
            'orderId' => $this->orderId,
            'customerId' => $this->customerId,
            'totalCents' => $this->totalCents,
            'paid' => $this->paid,
        ];
    }
}
