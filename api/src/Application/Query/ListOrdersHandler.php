<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\OrderSummary;
use App\Application\DTO\PaginatedResult;
use App\Domain\Repository\OrderRepositoryInterface;

final readonly class ListOrdersHandler implements ListOrdersHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function handle(ListOrdersQuery $listOrdersQuery): PaginatedResult
    {
        $page = max(1, $listOrdersQuery->page);
        $perPage = max(1, min(100, $listOrdersQuery->perPage));

        $result = $this->orderRepository->findPaginated($page, $perPage);

        $summaries = array_map(fn ($order) => new OrderSummary(
            $order->id()->toString(),
            $order->customerId(),
            $order->totalCents(),
            $order->isPaid()
        ), $result['items']);

        return new PaginatedResult($summaries, $result['total'], $page, $perPage);
    }
}
