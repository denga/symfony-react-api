<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\CreateOrderResult;

interface CreateOrderHandlerInterface
{
    public function handle(CreateOrderCommand $createOrderCommand): CreateOrderResult;
}
