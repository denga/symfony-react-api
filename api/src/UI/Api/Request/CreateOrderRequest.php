<?php

declare(strict_types=1);

namespace App\UI\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateOrderRequest
{
    #[Assert\NotBlank]
    public ?string $customerId = null;

    /**
     * @var array<int,array{sku:string,quantity:int,price_cents:int}>
     */
    #[Assert\NotNull]
    #[Assert\Count(min: 1, minMessage: 'Order must contain at least one item.')]
    public array $items = [];
}
