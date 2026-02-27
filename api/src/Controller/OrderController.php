<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Command\CreateOrderHandlerInterface;
use App\Application\Query\GetOrderHandler;
use App\Application\Query\GetOrderQuery;
use App\Application\Query\ListOrdersHandlerInterface;
use App\Application\Query\ListOrdersQuery;
use App\Domain\Model\OrderId;
use App\UI\Api\Mapper\OrderSummaryToResponseMapper;
use App\UI\Api\Mapper\RequestToCommandMapper;
use App\UI\Api\Request\CreateOrderRequest;
use App\UI\Api\Request\ListOrdersRequest;
use App\UI\Api\Response\CreateOrderResponse;
use App\UI\Api\Response\OrderDetailResponse;
use App\UI\Api\Response\OrdersListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly RequestToCommandMapper $requestToCommandMapper,
        private readonly OrderSummaryToResponseMapper $orderSummaryToResponseMapper,
        private readonly CreateOrderHandlerInterface $createOrderHandler,
        private readonly ListOrdersHandlerInterface $listOrdersHandler,
        private readonly GetOrderHandler $getOrderHandler,
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
    public function list(
        #[MapQueryString]
        ListOrdersRequest $listOrdersRequest,
    ): JsonResponse {
        $listOrdersQuery = new ListOrdersQuery($listOrdersRequest->page, $listOrdersRequest->perPage);
        $result = $this->listOrdersHandler->handle($listOrdersQuery);

        $items = $this->orderSummaryToResponseMapper->map($result->items);
        $ordersListResponse = new OrdersListResponse(
            $items,
            $result->total,
            $result->page,
            $result->perPage
        );

        return $this->json($ordersListResponse, 200);
    }

    #[Route('/api/orders/{id}', name: 'api_get_order', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        try {
            $orderId = OrderId::fromString($id);
        } catch (\InvalidArgumentException) {
            return $this->json(['error' => 'Invalid order ID'], 404);
        }

        $summary = $this->getOrderHandler->handle(new GetOrderQuery($orderId));

        if (null === $summary) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        return $this->json(new OrderDetailResponse(
            $summary->orderId,
            $summary->customerId,
            $summary->totalCents,
            $summary->paid
        ), 200);
    }
}
