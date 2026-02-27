<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\OrderSummary;
use App\Domain\Repository\OrderRepositoryInterface;

final readonly class GetOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function handle(GetOrderQuery $query): ?OrderSummary
    {
        $order = $this->orderRepository->findById($query->orderId);
        if (null === $order) {
            return null;
        }

        return new OrderSummary(
            $order->id()->toString(),
            $order->customerId(),
            $order->totalCents(),
            $order->isPaid()
        );
    }
}
