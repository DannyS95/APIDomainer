<?php

namespace App\Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class DoctrineRepository implements DoctrineRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function createQueryBuilder(string $entityClass, string $entityAlias): self
    {
        $this->qb = $this->entityManager->createQueryBuilder()
            ->select($entityAlias)
            ->from($entityClass, $entityAlias);

        return $this;
    }

    public function whereEqual(string $field, mixed $value): self
    {
        $this->qb->andWhere("{$this->qb->getRootAliases()[0]}.$field = :$field")
                 ->setParameter($field, $value);

        return $this;
    }

    public function whereLike(string $field, mixed $value): self
    {
        $this->qb->andWhere("{$this->qb->getRootAliases()[0]}.$field LIKE :$field")
                 ->setParameter($field, "%$value%");

        return $this;
    }

    public function buildSorts(array $sorts): self
    {
        foreach ($sorts as $field => $order) {
            $this->qb->addOrderBy($field, $order);
        }
        return $this;
    }

    public function buildPagination(int $page, int $itemsPerPage): self
    {
        $this->qb->setFirstResult(($page - 1) * $itemsPerPage)
                 ->setMaxResults($itemsPerPage);

        return $this;
    }

    public function fetchArray(): array
    {
        return $this->qb->getQuery()->getArrayResult();
    }

    public function findOne(): ?object
    {
        return $this->qb->getQuery()->getOneOrNullResult();
    }

    public function persist(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
