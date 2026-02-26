<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\OrderSummary;
use App\Domain\Repository\OrderRepositoryInterface;

final readonly class ListOrdersHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * @return array{items: OrderSummary[], total:int}
     */
    public function handle(ListOrdersQuery $listOrdersQuery): array
    {
        $page = max(1, $listOrdersQuery->page);
        $perPage = max(1, min(100, $listOrdersQuery->perPage)); // Schutz gegen zu große requests

        $result = $this->orderRepository->findPaginated($page, $perPage);

        $summaries = array_map(fn ($order) => new OrderSummary(
            $order->id()->toString(),
            $order->customerId(),
            $order->totalCents(),
            $order->isPaid()
        ), $result['items']);

        return [
            'items' => $summaries,
            'total' => $result['total'],
        ];
    }
}
