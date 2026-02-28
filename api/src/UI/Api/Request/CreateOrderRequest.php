<?php

declare(strict_types=1);

namespace App\UI\Api\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    title: 'CreateOrderRequest',
    description: 'Request schema for creating an order',
    required: ['customerId', 'items'],
)]
final class CreateOrderRequest
{
    #[OA\Property(description: 'Customer ID', example: 'customer-test-123')]
    #[Assert\NotBlank]
    public ?string $customerId = null;

    /**
     * @var array<int, array{sku: string, quantity: int, price_cents: int}>
     */
    #[OA\Property(type: 'array', items: new OA\Items(
        required: ['sku', 'quantity', 'price_cents'],
        properties: [
            new OA\Property(property: 'sku', type: 'string', minLength: 1),
            new OA\Property(property: 'quantity', type: 'integer', minimum: 1),
            new OA\Property(property: 'price_cents', type: 'integer', minimum: 0),
        ],
        type: 'object',
        example: [
            'sku' => 'sku-123',
            'quantity' => 1,
            'price_cents' => 1000,
        ]
    ), minItems: 1)]
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
