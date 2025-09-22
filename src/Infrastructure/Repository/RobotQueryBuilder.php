<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Robot;
use App\Infrastructure\Repository\DoctrineComparisonEnum;

final class RobotQueryBuilder
{
    private const ENTITY = Robot::class;
    private const ALIAS = 'r';

    private QueryBuilder $qb;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->qb = $entityManager->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(self::ENTITY, self::ALIAS);
    }

    /**
     * Add a WHERE clause to filter by ID.
     */
    public function whereId(int $id): self
    {
        $this->qb->andWhere(self::ALIAS . '.id = :id')
                 ->setParameter('id', $id);

        return $this;
    }

    /**
     * Add multiple WHERE clauses based on filters and operations.
     */
    public function whereClauses(array $filters, array $operations): self
    {
        foreach ($filters as $filter => $value) {
            $operation = $operations[$filter] ?? DoctrineComparisonEnum::eq->value;

            if (!DoctrineComparisonEnum::tryFrom($operation)) {
                throw new \InvalidArgumentException("Invalid operation: $operation");
            }

            $this->qb->andWhere(self::ALIAS . ".$filter $operation :$filter")
                     ->setParameter($filter, $value);
        }
        return $this;
    }

    /**
     * Add sorting to the query.
     */
    public function addSorts(array $sorts): self
    {
        foreach ($sorts as $field => $order) {
            $this->qb->addOrderBy(self::ALIAS . ".$field", $order);
        }
        return $this;
    }

    /**
     * Add pagination to the query.
     */
    public function paginate(int $page, int $itemsPerPage): self
    {
        $this->qb->setFirstResult(($page - 1) * $itemsPerPage)
                 ->setMaxResults($itemsPerPage);
        return $this;
    }

    /**
     * Execute the query and return the result as an array.
     */
    public function fetchArray(): array
    {
        return $this->qb->getQuery()->getArrayResult();
    }

    /**
     * Execute the query and return a single result.
     */
    public function fetchOne(): ?Robot
    {
        return $this->qb->getQuery()->getOneOrNullResult();
    }
}
