<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\CreateOrderResult;
use App\Domain\Event\DomainEventPublisherInterface;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Domain\Service\OrderFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\API\Trace\TracerProviderInterface;
use Psr\Log\LoggerInterface;

final readonly class CreateOrderHandler implements CreateOrderHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderFactoryInterface $orderFactory,
        private DomainEventPublisherInterface $domainEventPublisher,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private TracerProviderInterface $tracerProvider,
    ) {
    }

    public function handle(CreateOrderCommand $createOrderCommand): CreateOrderResult
    {
        $tracer = $this->tracerProvider->getTracer('order-api');
        $span = $tracer->spanBuilder('create_order')
            ->setSpanKind(SpanKind::KIND_INTERNAL)
            ->setAttribute('order.customer_id', $createOrderCommand->customerId)
            ->setAttribute('order.items_count', \count($createOrderCommand->items))
            ->startSpan();

        $scope = $span->activate();

        try {
            $this->logger->info('Creating order', [
                'customerId' => $createOrderCommand->customerId,
                'itemCount' => \count($createOrderCommand->items),
            ]);

            $this->entityManager->beginTransaction();
            try {
                $order = $this->orderFactory->createNew($createOrderCommand->customerId, $createOrderCommand->items);

                $this->orderRepository->save($order);

                $this->entityManager->commit();

                $span->addEvent('order.persisted', [
                    'order.id' => $order->id()->toString(),
                ]);
            } catch (\Throwable $e) {
                $this->entityManager->rollback();
                throw $e;
            }

            $this->logger->info('Order created successfully', [
                'orderId' => $order->id()->toString(),
            ]);

            foreach ($order->releaseEvents() as $event) {
                $this->domainEventPublisher->publish($event);
            }

            $span->addEvent('order.events_published');

            $span->setAttribute('order.id', $order->id()->toString());
            $span->setAttribute('order.total_cents', $order->totalCents());
            $span->setStatus(StatusCode::STATUS_OK);

            return new CreateOrderResult($order->id()->toString());
        } catch (\Throwable $e) {
            $span->recordException($e);
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());

            $this->logger->error('Failed to create order', [
                'exception' => $e,
            ]);
            throw $e;
        } finally {
            $span->end();
            $scope->detach();
        }
    }
}
