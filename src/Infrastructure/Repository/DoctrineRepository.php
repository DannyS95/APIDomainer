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

    public function __construct(ManagerRegistry $registry, string $entityClass, string $entityAlias)
    {
        $this->serviceRepo = new ServiceEntityRepository(registry: $registry, entityClass: Robot::class);
        $this->entityClass = $entityClass;
        $this->entityAlias = $entityAlias;
        $this->qb = $this->serviceRepo->createQueryBuilder($this->entityAlias);
    }

    public function buildClauses(?array $filters, ?array $operations)
    {
        foreach ($filters as $filter => $value) {
            $operator = DoctrineComparisonEnum::fromName($operations[$filter]);

            $expr = new Comparison("{$this->entityAlias}.$filter", $operator, $value);

            $this->qb->andWhere($expr);
        }

        return $this;
    }

    public function buildPagination(?int $page, ?int $itemsPerPage)
    {
        if ($page) {
            $this->qb->setFirstResult($page - 1);
            $this->qb->setMaxResults($itemsPerPage);
        }

        return $this;
    }

    public function buildSorts(?array $sorts)
    {
        $criteria = new Criteria();
        $criteria->orderBy($sorts);
        $this->qb->addCriteria($criteria);

        return $this;
    }

    public function fetchArray(): array
    {
        return $this->qb->getQuery()->getArrayResult();
    }
}
