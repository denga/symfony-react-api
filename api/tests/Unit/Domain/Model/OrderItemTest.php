<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\OrderItem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OrderItemTest extends TestCase
{
    public function testCreatesValidOrderItem(): void
    {
        $orderItem = new OrderItem('SKU-001', 2, 1000);

        $this->assertSame('SKU-001', $orderItem->sku());
        $this->assertSame(2, $orderItem->quantity());
        $this->assertSame(1000, $orderItem->unitPriceCents());
        $this->assertSame(2000, $orderItem->totalCents());
    }

    public function testZeroPriceIsAllowed(): void
    {
        $orderItem = new OrderItem('FREE-ITEM', 1, 0);
        $this->assertSame(0, $orderItem->totalCents());
    }

    #[DataProvider('invalidQuantityProvider')]
    public function testThrowsOnInvalidQuantity(int $quantity): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be > 0');

        new OrderItem('SKU-001', $quantity, 100);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function invalidQuantityProvider(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
        ];
    }

    public function testThrowsOnNegativePrice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be >= 0');

        new OrderItem('SKU-001', 1, -100);
    }
}
