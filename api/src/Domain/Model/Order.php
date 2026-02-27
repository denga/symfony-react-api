<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Event\OrderPlaced;

final class Order
{
    /**
     * @var OrderItem[]
     */
    private readonly array $items;
    private bool $paid = false;
    /**
     * @var array<int, object> Domain events collected transiently
     */
    private array $recordedEvents = [];

    /**
     * @param OrderItem[] $items
     */
    public function __construct(
        private readonly OrderId $orderId,
        private readonly string $customerId,
        array $items,
    ) {
        if ([] === $items) {
            throw new \InvalidArgumentException('Order must contain at least one item.');
        }
        $this->items = $items;
    }

    /**
     * Factory for reconstituting an Order from persistence (e.g. Doctrine).
     * Use this instead of the constructor when the order was already paid.
     *
     * @param OrderItem[] $items
     */
    public static function fromPersistence(OrderId $orderId, string $customerId, array $items, bool $paid): self
    {
        $order = new self($orderId, $customerId, $items);
        if ($paid) {
            $order->paid = true;
        }

        return $order;
    }

    public function id(): OrderId
    {
        return $this->orderId;
    }

    public function customerId(): string
    {
        return $this->customerId;
    }

    /**
     * @return OrderItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function markPaid(): void
    {
        if ($this->paid) {
            throw new \LogicException('Order already paid.');
        }
        $this->paid = true;
        $this->recordEvent(new OrderPlaced($this->orderId->toString()));
    }

    /**
     * @return object[] recorded domain events. Application layer should grab & dispatch.
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    public function totalCents(): int
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->totalCents();
        }

        return $sum;
    }

    private function recordEvent(object $event): void
    {
        $this->recordedEvents[] = $event;
    }
}
