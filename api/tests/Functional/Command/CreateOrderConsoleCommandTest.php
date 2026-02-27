<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Application\Command\CreateOrderCommand;
use App\Application\Command\CreateOrderHandlerInterface;
use App\Application\DTO\CreateOrderResult;
use App\Command\CreateOrderConsoleCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateOrderConsoleCommandTest extends TestCase
{
    public function testExecuteCreatesOrderAndOutputsId(): void
    {
        $createOrderResult = new CreateOrderResult('order-uuid-123');

        $mockHandler = $this->createMock(CreateOrderHandlerInterface::class);
        $mockHandler->expects($this->once())
            ->method('handle')
            ->with(self::isInstanceOf(CreateOrderCommand::class))
            ->willReturn($createOrderResult);

        $createOrderConsoleCommand = new CreateOrderConsoleCommand($mockHandler);

        $commandTester = new CommandTester($createOrderConsoleCommand);

        $exitCode = $commandTester->execute([
            'customerId' => 'cust-123',
            '--item' => ['sku-1:2:1000', 'sku-2:1:500'],
        ]);

        $output = $commandTester->getDisplay();

        // Assertions
        $this->assertSame(0, $exitCode); // Command::SUCCESS
        $this->assertStringContainsString('Order created: order-uuid-123', $output);
    }

    public function testExecuteInvalidItemFormatReturnsInvalid(): void
    {
        $mockHandler = $this->createMock(CreateOrderHandlerInterface::class);
        $mockHandler->expects($this->never())->method('handle');

        $createOrderConsoleCommand = new CreateOrderConsoleCommand($mockHandler);
        $commandTester = new CommandTester($createOrderConsoleCommand);

        $exitCode = $commandTester->execute([
            'customerId' => 'cust-123',
            '--item' => ['invalid-format'],
        ]);

        $output = $commandTester->getDisplay();
        $this->assertSame(2, $exitCode);
        $this->assertStringContainsString('Invalid item format', $output);
    }
}
