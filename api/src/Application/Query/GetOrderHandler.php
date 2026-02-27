<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\OrderSummary;
use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Repository\OrderRepositoryInterface;

final readonly class GetOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function handle(GetOrderQuery $getOrderQuery): ?OrderSummary
    {
        try {
            $orderId = OrderId::fromString($getOrderQuery->orderId);
        } catch (\InvalidArgumentException) {
            return null;
        }

        $order = $this->orderRepository->findById($orderId);
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
