<?php

declare(strict_types=1);

namespace App\Infrastructure\Event;

use App\Domain\Event\DomainEventPublisherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class SymfonyDomainEventPublisher implements DomainEventPublisherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function publish(object $event): void
    {
        $this->logger->info('Publishing domain event', ['event' => $event::class]);

        $this->eventDispatcher->dispatch($event);
    }
}
