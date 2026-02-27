<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\CreateOrderResult;
use App\Domain\Event\DomainEventPublisherInterface;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Domain\Service\OrderFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class CreateOrderHandler implements CreateOrderHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderFactoryInterface $orderFactory,
        private DomainEventPublisherInterface $domainEventPublisher,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(CreateOrderCommand $createOrderCommand): CreateOrderResult
    {
        $this->entityManager->beginTransaction();
        try {
            // Create domain entity
            $order = $this->orderFactory->createNew($createOrderCommand->customerId, $createOrderCommand->items);

            // Persist using repository (infrastructure implements)
            $this->orderRepository->save($order);

            // commit
            $this->entityManager->commit();

            foreach ($order->releaseEvents() as $event) {
                $this->domainEventPublisher->publish($event);
            }

            return new CreateOrderResult($order->id()->toString());
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Failed to create order', [
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
