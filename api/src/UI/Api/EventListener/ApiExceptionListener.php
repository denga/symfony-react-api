<?php

declare(strict_types=1);
// src/UI/Api/EventListener/ApiExceptionListener.php

namespace App\UI\Api\EventListener;

use App\UI\Api\Exception\ApiValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Translates exceptions to JSON error responses.
 * Map:
 *  - BadRequestHttpException -> 400 (or 422 if you prefer)
 *  - ApiValidationException (custom) -> 422
 *  - HttpExceptionInterface -> use its status code.
 *  - other exceptions -> 500 (with generic message in prod).
 */
final readonly class ApiExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private bool $debug = false,
    ) {
    }

    public function onKernelException(ExceptionEvent $exceptionEvent): void
    {
        $throwable = $exceptionEvent->getThrowable();
        $status = 500;
        $payload = [
            'error' => [
                'type' => $throwable::class,
                'message' => 'An internal error occurred.',
            ],
        ];

        // HttpExceptionInterface provides status code and headers
        if ($throwable instanceof HttpExceptionInterface) {
            $status = $throwable->getStatusCode();
            $payload['error']['message'] = $throwable->getMessage() ?: JsonResponse::$statusTexts[$status] ?? 'Error';
        }

        // BadRequest (or thrown by ArgumentResolver) -> map to 400/422 with details if present
        if ($throwable instanceof BadRequestHttpException) {
            $status = 400;
            $payload['error']['message'] = $throwable->getMessage() ?: 'Bad request';
        }

        // Custom validation exception (you can create this class) -> 422 Unprocessable Entity
        if ($throwable instanceof ApiValidationException) {
            $status = 422;
            $payload['error']['message'] = $throwable->getMessage();
            $payload['error']['violations'] = $throwable->getViolations(); // array of ['path'=>..., 'message'=>...]
        }

        // If debug, include trace
        if ($this->debug) {
            $payload['error']['exception'] = [
                'class' => $throwable::class,
                'trace' => $throwable->getTraceAsString(),
            ];
        } elseif (500 === $status) {
            // Avoid leaking internal message on 500
            $payload['error']['message'] = 'Internal server error';
        }

        $jsonResponse = new JsonResponse($payload, $status);
        $exceptionEvent->setResponse($jsonResponse);

        // log server errors
        if ($status >= 500) {
            $this->logger->error('Unhandled exception caught by ApiExceptionListener', [
                'exception' => $throwable,
            ]);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }
}
