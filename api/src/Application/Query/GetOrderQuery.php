<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Model\OrderId;

final readonly class GetOrderQuery
{
    public function __construct(
        public OrderId $orderId,
    ) {
    }
}
