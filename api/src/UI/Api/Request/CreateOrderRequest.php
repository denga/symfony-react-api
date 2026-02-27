<?php

declare(strict_types=1);

namespace App\UI\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateOrderRequest
{
    #[Assert\NotBlank]
    public ?string $customerId = null;

    /**
     * @var array<int, array{sku: string, quantity: int, price_cents: int}>
     */
    #[Assert\NotNull]
    #[Assert\Count(min: 1, minMessage: 'Order must contain at least one item.')]
    #[Assert\All([
        new Assert\Collection(
            fields: [
                'sku' => [new Assert\Required(), new Assert\NotBlank(message: 'Item SKU must not be blank.')],
                'quantity' => [new Assert\Required(), new Assert\Positive(message: 'Item quantity must be positive.')],
                'price_cents' => [new Assert\Required(), new Assert\GreaterThanOrEqual(0, message: 'Item price_cents must be >= 0.')],
            ],
            allowExtraFields: false,
            allowMissingFields: false,
        ),
    ])]
    public array $items = [];
}
