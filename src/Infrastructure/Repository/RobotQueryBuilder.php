<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Robot;

final class RobotQueryBuilder
{
    private QueryBuilder $qb;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->qb = $entityManager->createQueryBuilder()
            ->select('r')
            ->from(Robot::class, 'r');
    }

    /**
     * Add a WHERE clause to filter by ID.
     */
    public function whereId(int $id): self
    {
        $this->qb->andWhere('r.id = :id')
                 ->setParameter('id', $id);

        return $this;
    }

    /**
     * Add multiple WHERE clauses based on filters and operations.
     */
    public function whereClauses(array $filters, array $operations): self
    {
        foreach ($filters as $filter => $value) {
            $operation = $operations[$filter] ?? '=';
            $this->qb->andWhere("r.$filter $operation :$filter")
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
            $this->qb->addOrderBy("r.$field", $order);
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
