<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Command\CreateOrderHandler;
use App\UI\Api\Mapper\RequestToCommandMapper;
use App\UI\Api\Request\CreateOrderRequest;
use App\UI\Api\Response\CreateOrderResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly RequestToCommandMapper $requestToCommandMapper,
        private readonly CreateOrderHandler $createOrderHandler,
    ) {
    }

    #[Route('/api/orders', name: 'api_create_order', methods: ['POST'])]
    public function create(
        #[MapRequestPayload]
        CreateOrderRequest $createOrderRequest,
    ): JsonResponse {
        $createOrderCommand = $this->requestToCommandMapper->map($createOrderRequest);
        $createOrderResult = $this->createOrderHandler->handle($createOrderCommand);
        $createOrderResponse = new CreateOrderResponse($createOrderResult->orderId, '/api/orders/'.$createOrderResult->orderId);

        return $this->json($createOrderResponse, 201);
    }
}
