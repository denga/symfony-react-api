<?php

declare(strict_types=1);

namespace App\UI\Api\Response;

final class OrdersListResponse implements \JsonSerializable
{
    /**
     * @param array<int,array{orderId:string,customerId:string,totalCents:int,paid:bool}> $items
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
