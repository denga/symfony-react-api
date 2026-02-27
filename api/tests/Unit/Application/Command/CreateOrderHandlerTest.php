<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command;

use App\Application\Command\CreateOrderCommand;
use App\Application\Command\CreateOrderHandler;
use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Model\OrderItem;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Domain\Service\OrderFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class CreateOrderHandlerTest extends TestCase
{
    public function testHandleCreatesOrderAndReturnsResult(): void
    {
        $orderId = OrderId::generate();
        $items = [new OrderItem('sku-1', 2, 1000)];
        $order = new Order($orderId, 'cust-123', $items);

        $mockOrderFactory = $this->createMock(OrderFactoryInterface::class);
        $mockOrderFactory->expects($this->once())
            ->method('createNew')
            ->with('cust-123', [[
                'sku' => 'sku-1',
                'quantity' => 2,
                'price_cents' => 1000,
            ]])
            ->willReturn($order);

        $mockOrderRepository = $this->createMock(OrderRepositoryInterface::class);
        $mockOrderRepository->expects($this->once())
            ->method('save')
            ->with($order);

        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects($this->once())->method('beginTransaction');
        $mockEntityManager->expects($this->once())->method('commit');

        $mockLogger = $this->createStub(LoggerInterface::class);

        $createOrderHandler = new CreateOrderHandler(
            $mockOrderRepository,
            $mockOrderFactory,
            $mockEntityManager,
            $mockLogger,
        );

        $createOrderCommand = new CreateOrderCommand('cust-123', [
            [
                'sku' => 'sku-1',
                'quantity' => 2,
                'price_cents' => 1000,
            ],
        ]);

        $createOrderResult = $createOrderHandler->handle($createOrderCommand);

        $this->assertEquals($orderId->toString(), $createOrderResult->orderId);
    }

    public function testHandleRollsBackAndRethrowsOnFailure(): void
    {
        $mockOrderFactory = $this->createMock(OrderFactoryInterface::class);
        $mockOrderFactory->expects($this->once())
            ->method('createNew')
            ->willThrowException(new \RuntimeException('DB error'));

        $mockOrderRepository = $this->createMock(OrderRepositoryInterface::class);
        $mockOrderRepository->expects($this->never())->method('save');

        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntityManager->expects($this->once())->method('beginTransaction');
        $mockEntityManager->expects($this->once())->method('rollback');
        $mockEntityManager->expects($this->never())->method('commit');

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects($this->once())->method('error');

        $createOrderHandler = new CreateOrderHandler(
            $mockOrderRepository,
            $mockOrderFactory,
            $mockEntityManager,
            $mockLogger,
        );

        $createOrderCommand = new CreateOrderCommand('cust-123', [
            [
                'sku' => 'sku-1',
                'quantity' => 1,
                'price_cents' => 500,
            ],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DB error');

        $createOrderHandler->handle($createOrderCommand);
    }
}
