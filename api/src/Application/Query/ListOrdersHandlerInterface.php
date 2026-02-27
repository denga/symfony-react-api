<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\PaginatedResult;

interface ListOrdersHandlerInterface
{
    /**
     * @return PaginatedResult<\App\Application\DTO\OrderSummary>
     */
    public function handle(ListOrdersQuery $query): PaginatedResult;
}
