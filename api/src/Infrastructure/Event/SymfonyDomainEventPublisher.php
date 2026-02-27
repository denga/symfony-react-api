<?php

declare(strict_types=1);

namespace App\Infrastructure\Event;

use App\Domain\Event\DomainEventPublisherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class SymfonyDomainEventPublisher implements DomainEventPublisherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function publish(object $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
