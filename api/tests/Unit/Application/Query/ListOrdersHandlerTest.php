<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Query;

use App\Application\Query\ListOrdersHandler;
use App\Application\Query\ListOrdersQuery;
use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Model\OrderItem;
use App\Domain\Repository\OrderRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ListOrdersHandlerTest extends TestCase
{
    public function testHandleReturnsSummariesAndTotal(): void
    {
        // Arrange: create a fake domain Order to be returned by repository
        $orderId = OrderId::generate();
        $items = [new OrderItem('sku-1', 2, 1000)];
        $order = new Order($orderId, 'cust-1', $items);

        $mockRepo = $this->createMock(OrderRepositoryInterface::class);
        $mockRepo->expects($this->once())
            ->method('findPaginated')
            ->with(1, 10)
            ->willReturn([
                'items' => [$order],
                'total' => 1,
            ]);

        $listOrdersHandler = new ListOrdersHandler($mockRepo);

        $listOrdersQuery = new ListOrdersQuery(1, 10);

        // Act
        $result = $listOrdersHandler->handle($listOrdersQuery);

        // Assert
        $this->assertCount(1, $result->items);
        $this->assertEquals(1, $result->total);

        $summary = $result->items[0];
        $this->assertEquals($orderId->toString(), $summary->orderId);
        $this->assertEquals('cust-1', $summary->customerId);
        $this->assertEquals(2000, $summary->totalCents); // 2 * 1000
        $this->assertFalse($summary->paid);
    }
}
