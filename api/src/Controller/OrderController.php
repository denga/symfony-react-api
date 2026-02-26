<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Command\CreateOrderHandler;
use App\Application\DTO\OrderSummary;
use App\Application\Query\ListOrdersHandler;
use App\Application\Query\ListOrdersQuery;
use App\UI\Api\Mapper\RequestToCommandMapper;
use App\UI\Api\Request\CreateOrderRequest;
use App\UI\Api\Response\CreateOrderResponse;
use App\UI\Api\Response\OrdersListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly RequestToCommandMapper $requestToCommandMapper,
        private readonly CreateOrderHandler $createOrderHandler,
        private readonly ListOrdersHandler $listOrdersHandler,
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

    #[Route('/api/orders', name: 'api_list_orders', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = max(1, $request->query->getInt('perPage', 20));

        $listOrdersQuery = new ListOrdersQuery($page, $perPage);
        $result = $this->listOrdersHandler->handle($listOrdersQuery);

        /** @var array<int, array{orderId: string, customerId: string, totalCents: int, paid: bool}> $items */
        $items = array_values(array_map(fn (OrderSummary $orderSummary): array => [
            'orderId' => $orderSummary->orderId,
            'customerId' => $orderSummary->customerId,
            'totalCents' => $orderSummary->totalCents,
            'paid' => $orderSummary->paid,
        ], $result['items']));

        $ordersListResponse = new OrdersListResponse($items, $result['total'], $page, $perPage);

        return $this->json($ordersListResponse, 200);
    }
}
