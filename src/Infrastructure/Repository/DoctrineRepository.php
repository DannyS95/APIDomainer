<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Comparison;
use App\Infrastructure\DTO\ApiFiltersDTO;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use App\Infrastructure\DoctrineComparisonEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class DoctrineRepository
{
    private ServiceEntityRepository $serviceRepo;

    private string $entityClass;

    private string $entityAlias;

    private QueryBuilder $qb;

    protected function __construct(ManagerRegistry $registry, string $entityClass, string $entityAlias)
    {
        $this->serviceRepo = new ServiceEntityRepository(registry: $registry, entityClass: $entityClass);
        $this->entityClass = $entityClass;
        $this->entityAlias = $entityAlias;
        $this->qb = $this->serviceRepo->createQueryBuilder($this->entityAlias);
    }

    protected function buildClauses(?array $filters, ?array $operations)
    {
        foreach ($filters as $filter => $value) {
            $operator = DoctrineComparisonEnum::fromName($operations[$filter]);

            if ($operations[$filter] === DoctrineComparisonEnum::lk->name) {
                $this->qb->andWhere($this->qb->expr()->andX(
                    $this->qb->expr()->like("{$this->entityAlias}.$filter", ":{$filter}")
                ))->setParameter($filter, "%{$value}%");

                continue;
            }

            $expr = new Comparison("{$this->entityAlias}.$filter", $operator, ":{%$filter}");

            $this->qb->andWhere($expr)->setParameter($filter, $value);
        }

        return $this;
    }

    protected function buildPagination(?int $page, ?int $itemsPerPage)
    {
        if ($page) {
            $this->qb->setFirstResult($page - 1);
            $this->qb->setMaxResults($itemsPerPage);
        }

        return $this;
    }

    protected function buildSorts(?array $sorts)
    {
        $criteria = new Criteria();
        $criteria->orderBy($sorts);
        $this->qb->addCriteria($criteria);

        return $this;
    }

    protected function fetchArray(): array
    {
        return $this->qb->getQuery()->getResult();
    }

    protected function persist(object $entity)
    {
        $this->qb->getEntityManager()->persist($entity);
    }

    protected function save()
    {
        $this->qb->getEntityManager()->flush();
        $this->qb = $this->serviceRepo->createQueryBuilder($this->entityAlias);
    }

    protected function serviceRepo(): ?object
    {
        return $this->serviceRepo;
    }
}
