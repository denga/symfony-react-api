<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Order;
use App\Domain\Model\OrderId;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(OrderId $orderId): ?Order;
}
