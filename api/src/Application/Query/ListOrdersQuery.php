<?php

declare(strict_types=1);

namespace App\Application\Query;

use OpenApi\Attributes as OA;

final class ListOrdersQuery
{
    public function __construct(
        #[OA\Parameter(
            name: 'page',
            description: 'Page number',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer'),
            example: 1
        )]
        public int $page = 1,

        #[OA\Parameter(
            name: 'perPage',
            description: 'Number of items per page',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer'),
            example: 20
        )]
        public int $perPage = 20,
    ) {
    }
}
