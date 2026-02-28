<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Command\CreateOrderHandlerInterface;
use App\Application\DTO\OrderSummary;
use App\Application\Query\GetOrderHandler;
use App\Application\Query\GetOrderQuery;
use App\Application\Query\ListOrdersHandlerInterface;
use App\Application\Query\ListOrdersQuery;
use App\UI\Api\Mapper\OrderSummaryToResponseMapper;
use App\UI\Api\Mapper\RequestToCommandMapper;
use App\UI\Api\Request\CreateOrderRequest;
use App\UI\Api\Request\ListOrdersRequest;
use App\UI\Api\Response\CreateOrderResponse;
use App\UI\Api\Response\OrderDetailResponse;
use App\UI\Api\Response\OrdersListResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Orders', description: 'Operations to create and list orders')]
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
    #[OA\Response(response: 201, description: 'Order created', content: new OA\JsonContent(ref: new Model(type: CreateOrderResponse::class)))]
    #[OA\Response(response: 400, description: 'Bad request')]
    #[OA\Response(response: 422, description: 'Validation failed')]
    #[OA\Response(response: 500, description: 'Service unavailable')]
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
    #[OA\Response(response: 200, description: 'Orders list', content: new OA\JsonContent(ref: new Model(type: OrdersListResponse::class)))]
    public function list(
        #[MapQueryString]
        ListOrdersRequest $listOrdersRequest,
    ): JsonResponse {
        $listOrdersQuery = new ListOrdersQuery($listOrdersRequest->page, $listOrdersRequest->perPage);
        $paginatedResult = $this->listOrdersHandler->handle($listOrdersQuery);

        $items = $this->orderSummaryToResponseMapper->map($paginatedResult->items);
        $ordersListResponse = new OrdersListResponse(
            $items,
            $paginatedResult->total,
            $paginatedResult->page,
            $paginatedResult->perPage
        );

        return $this->json($ordersListResponse, 200);
    }

    #[Route('/api/orders/{id}', name: 'api_get_order', methods: ['GET'])]
    #[OA\Parameter(name: 'id', description: 'Order ID', in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'order-123')]
    #[OA\Response(response: 200, description: 'Order details', content: new OA\JsonContent(ref: new Model(type: OrderDetailResponse::class)))]
    public function get(string $id): JsonResponse
    {
        $summary = $this->getOrderHandler->handle(new GetOrderQuery($id));

        if (! $summary instanceof OrderSummary) {
            return $this->json([
                'error' => 'Order not found',
            ], 404);
        }

        return $this->json(new OrderDetailResponse(
            $summary->orderId,
            $summary->customerId,
            $summary->totalCents,
            $summary->paid
        ), 200);
    }
}
