<?php

declare(strict_types=1);

namespace App\UI\Api\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'CreateOrderResponse',
    description: 'Response schema for creating an order',
    required: ['orderId', 'orderUrl'],
)]
final class CreateOrderResponse implements \JsonSerializable
{
    public function __construct(
        public string $orderId,
        public string $orderUrl,
    ) {
    }

    /**
     * @return array{orderId:string,orderUrl:string}
     */
    public function jsonSerialize(): array
    {
        return [
            'orderId' => $this->orderId,
            'orderUrl' => $this->orderUrl,
        ];
    }
}
