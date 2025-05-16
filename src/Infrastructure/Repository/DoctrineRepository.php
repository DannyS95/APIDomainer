<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\Common\Collections\Criteria;
use App\Infrastructure\DoctrineComparisonEnum;

final class DoctrineRepository implements DoctrineRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Create the QueryBuilder instance with the provided entity and alias.
     */
    public function createQueryBuilder(string $entityClass, string $entityAlias): self
    {
        $this->qb = $this->entityManager->createQueryBuilder()
            ->select($entityAlias)
            ->from($entityClass, $entityAlias);

        return $this;
    }

    /**
     * Apply filters and operations to the QueryBuilder.
     */
    public function buildClauses(?array $filters, ?array $operations): self
    {
        foreach ($filters as $filter => $value) {
            $operator = DoctrineComparisonEnum::fromName($operations[$filter]);

            if ($operations[$filter] === DoctrineComparisonEnum::lk->name) {
                $this->qb->andWhere($this->qb->expr()->andX(
                    $this->qb->expr()->like("{$this->qb->getRootAliases()[0]}.{$filter}", ":{$filter}")
                ))->setParameter($filter, "%{$value}%");
                continue;
            }

            $expr = new Comparison("{$this->qb->getRootAliases()[0]}.{$filter}", $operator, ":{$filter}");
            $this->qb->andWhere($expr)->setParameter($filter, $value);
        }
        return $this;
    }

    /**
     * Apply sorting to the QueryBuilder.
     */
    public function buildSorts(?array $sorts): self
    {
        $criteria = new Criteria();
        $criteria->orderBy($sorts);
        $this->qb->addCriteria($criteria);

        return $this;
    }

    /**
     * Apply pagination to the QueryBuilder.
     */
    public function buildPagination(?int $page, ?int $itemsPerPage): self
    {
        if ($page) {
            $this->qb->setFirstResult(($page - 1) * $itemsPerPage)
                     ->setMaxResults($itemsPerPage);
        }
        return $this;
    }

    /**
     * Fetch the results as an array.
     */
    public function fetchArray(): array
    {
        return $this->qb->getQuery()->getArrayResult();
    }

    /**
     * Persist an entity in the database.
     */
    public function persist(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    /**
     * Save changes to the database.
     */
    public function save(): void
    {
        $this->entityManager->flush();
    }

    /**
     * Get the repository instance.
     */
    public function serviceRepo(): ?object
    {
        return $this->entityManager;
    }
}
