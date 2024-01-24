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

    public function buildClauses(ApiFiltersDTO $apiFiltersDto)
    {
        foreach ($apiFiltersDto->getFilters() as $filter => $value) {
            $operator = DoctrineComparisonEnum::fromName($apiFiltersDto->getOperations()[$filter]);

            $expr = new Comparison("{$this->entityAlias}.$filter", $operator, $value);

            $this->qb->andWhere($expr);
        }
    }

    public function buildPagination(ApiFiltersDTO $apiFiltersDto)
    {
        if ($apiFiltersDto->getPage()) { # doctrine has a bug where no results come with id eq and page >0
            $this->qb->setFirstResult($apiFiltersDto->getPage() - 1);
            $this->qb->setMaxResults($apiFiltersDto->getItemsPerPage());
        }
    }

    public function buildSorts(ApiFiltersDTO $apiFiltersDto)
    {
        $criteria = new Criteria();
        $criteria->orderBy($apiFiltersDto->getSorts());
        $this->qb->addCriteria($criteria);
    }

    public function fetchArray(): array
    {
        return $this->qb->getQuery()->getArrayResult();
    }
}
