<?php

declare(strict_types=1);

namespace App\UI\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class ListOrdersRequest
{
    public function __construct(
        #[Assert\Range(min: 1, max: 10000)]
        public int $page = 1,

        #[Assert\Range(min: 1, max: 100)]
        public int $perPage = 20,
    ) {
    }
}
