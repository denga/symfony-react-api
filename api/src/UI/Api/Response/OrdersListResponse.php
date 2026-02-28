<?php

declare(strict_types=1);

namespace App\UI\Api\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'OrdersListResponse',
    description: 'Response schema for listing orders',
    required: ['meta', 'data'],
    properties: [
        new OA\Property(property: 'meta', properties: [
            new OA\Property(property: 'total', type: 'integer'),
            new OA\Property(property: 'page', type: 'integer'),
            new OA\Property(property: 'perPage', type: 'integer'),
            new OA\Property(property: 'totalPages', type: 'integer'),
        ], type: 'object', example: '{"total": 100, "page": 1, "perPage": 20, "totalPages": 5}'),
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(
            properties: [
                new OA\Property(property: 'orderId', type: 'string'),
                new OA\Property(property: 'customerId', type: 'string'),
                new OA\Property(property: 'totalCents', type: 'integer'),
                new OA\Property(property: 'paid', type: 'boolean'),
            ],
            type: 'object'
        )),
    ]
)]
final readonly class OrdersListResponse implements \JsonSerializable
{
    /**
     * @param array<int, array{orderId: string, customerId: string, totalCents: int, paid: bool}> $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
    ) {
    }

    /**
     * @return array{meta: array{total: int, page: int, perPage: int, totalPages: int}, data: array<int, array{orderId: string, customerId: string, totalCents: int, paid: bool}>}
     */
    public function jsonSerialize(): array
    {
        $totalPages = (int) ceil($this->total / max(1, $this->perPage));

        return [
            'meta' => [
                'total' => $this->total,
                'page' => $this->page,
                'perPage' => $this->perPage,
                'totalPages' => $totalPages,
            ],
            'data' => $this->items,
        ];
    }
}
