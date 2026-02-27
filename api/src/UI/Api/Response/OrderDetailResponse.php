<?php

declare(strict_types=1);

namespace App\UI\Api\Response;

final class OrderDetailResponse implements \JsonSerializable
{
    public function __construct(
        public string $orderId,
        public string $customerId,
        public int $totalCents,
        public bool $paid,
    ) {
    }

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
