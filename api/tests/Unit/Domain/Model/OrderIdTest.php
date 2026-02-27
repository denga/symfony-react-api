<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\OrderId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OrderIdTest extends TestCase
{
    public function testFromStringAcceptsValidUuid(): void
    {
        $orderId = OrderId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $orderId->toString());
        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', (string) $orderId);
    }

    public function testGenerateCreatesValidId(): void
    {
        $orderId = OrderId::generate();
        $this->assertMatchesRegularExpression('/^[0-9a-f\-]{36}$/', $orderId->toString());
    }

    #[DataProvider('invalidIdProvider')]
    public function testThrowsOnInvalidId(string $invalidId): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid order id');

        OrderId::fromString($invalidId);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidIdProvider(): array
    {
        return [
            'empty' => [''],
            'too short' => ['abc'],
            'invalid chars' => ['xyz-1234-5678'],
        ];
    }
}
