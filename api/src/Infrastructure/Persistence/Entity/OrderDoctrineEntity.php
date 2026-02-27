<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class OrderDoctrineEntity
{
    /**
     * @param array<int, array{sku: string, quantity: int, price_cents: int}> $items
     */
    public function __construct(
        #[ORM\Id, ORM\Column(type: 'string', length: 36)]
        private string $id,
        #[ORM\Column(type: 'string')]
        private string $customerId,
        #[ORM\Column(type: 'json')]
        private array $items,
        #[ORM\Column(type: 'boolean')]
        private bool $paid = false,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * @return array<int, array{sku: string, quantity: int, price_cents: int}>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function markPaid(): void
    {
        $this->paid = true;
    }

    // Doctrine needs a no-arg constructor for hydration
    public static function forHydration(): self
    {
        return new self('', '', []);
    }
}
