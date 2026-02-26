<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\Uid\Uuid;

final readonly class OrderId implements \Stringable
{
    private string $id;

    private function __construct(string $id)
    {
        // basic validation
        if (! preg_match('/^[0-9a-fA-F\-]{8,}$/', $id)) {
            throw new \InvalidArgumentException('Invalid order id.');
        }
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toString());
    }

    public function toString(): string
    {
        return $this->id;
    }
}
