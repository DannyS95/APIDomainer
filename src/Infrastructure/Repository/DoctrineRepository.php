<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use App\Infrastructure\DoctrineComparisonEnum;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use App\Infrastructure\DTO\ApiFiltersDTO;
use Doctrine\Persistence\ManagerRegistry;
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
            print("{$this->entityAlias}.$filter");

            $this->qb->andWhere($expr);
        }
    }

    public function qb()
    {
        return $this->qb;
    }
}
