<?php

declare(strict_types=1);

namespace App\Application\DTO;

/**
 * @template T
 */
final readonly class PaginatedResult
{
    /**
     * @param array<int, T> $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
    ) {
    }

    public function totalPages(): int
    {
        return (int) ceil($this->total / max(1, $this->perPage));
    }
}
