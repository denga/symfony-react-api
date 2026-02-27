<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\OrderSummary;
use App\Domain\Model\Order;
use App\Domain\Repository\OrderRepositoryInterface;

final readonly class GetOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function handle(GetOrderQuery $getOrderQuery): ?OrderSummary
    {
        $order = $this->orderRepository->findById($getOrderQuery->orderId);
        if (! $order instanceof Order) {
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
