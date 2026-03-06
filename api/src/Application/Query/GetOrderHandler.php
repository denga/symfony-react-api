<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\OrderSummary;
use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Repository\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

final readonly class GetOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(GetOrderQuery $getOrderQuery): ?OrderSummary
    {
        $this->logger->info('Loading order', ['id' => $getOrderQuery->orderId]);

        try {
            $orderId = OrderId::fromString($getOrderQuery->orderId);
        } catch (\InvalidArgumentException) {
            $this->logger->warning('Order not found in repository', ['id' => $getOrderQuery->orderId]);

            return null;
        }

        $order = $this->orderRepository->findById($orderId);
        if (! $order instanceof Order) {
            $this->logger->warning('Order not found in repository', ['id' => $getOrderQuery->orderId]);

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
