<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\OrderSummary;
use App\Application\DTO\PaginatedResult;

interface ListOrdersHandlerInterface
{
    /**
     * @return PaginatedResult<OrderSummary>
     */
    public function handle(ListOrdersQuery $listOrdersQuery): PaginatedResult;
}
