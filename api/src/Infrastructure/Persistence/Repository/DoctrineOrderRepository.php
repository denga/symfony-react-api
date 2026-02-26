<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\Order;
use App\Domain\Model\OrderId;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Entity\OrderDoctrineEntity;
use App\Infrastructure\Persistence\Mapper\OrderMapper;
use Doctrine\ORM\EntityManagerInterface;

readonly class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Order $order): void
    {
        $orderDoctrineEntity = OrderMapper::toPersistence($order);
        // persist or merge depending on state
        $this->entityManager->persist($orderDoctrineEntity);
        $this->entityManager->flush();
    }

    public function findById(OrderId $orderId): ?Order
    {
        $do = $this->entityManager->getRepository(OrderDoctrineEntity::class)->find($orderId->toString());
        if (null === $do) {
            return null;
        }

        return OrderMapper::toDomain($do);
    }
}
