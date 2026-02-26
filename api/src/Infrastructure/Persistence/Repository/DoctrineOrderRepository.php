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

    public function findPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(OrderDoctrineEntity::class, 'o')
            ->orderBy('o.id', 'DESC'); // oder created_at wenn vorhanden

        $firstResult = max(0, ($page - 1) * $perPage);
        $query = $queryBuilder->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($perPage);

        /** @var list<OrderDoctrineEntity> $doctrineEntities */
        $doctrineEntities = $query->getResult();

        $countQb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from(OrderDoctrineEntity::class, 'o');

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $orders = array_map(OrderMapper::toDomain(...), $doctrineEntities);

        return [
            'items' => $orders,
            'total' => $total,
        ];
    }
}
