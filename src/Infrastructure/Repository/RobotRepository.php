<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use App\Application\DTO\ApiFiltersDTO;
use Doctrine\Persistence\ManagerRegistry;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Infrastructure\Repository\DoctrineRepository;

final class RobotRepository extends DoctrineRepository implements RobotRepositoryInterface
{
    private const ALIAS = 'robot';
    private const ENTITY = Robot::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(registry: $registry, entityClass: self::ENTITY, entityAlias: self::ALIAS);
    }

    public function findAll(ApiFiltersDTO $apiFiltersDTO): array
    {
       return $this->buildClauses(filters: $apiFiltersDTO->getFilters(), operations: $apiFiltersDTO->getOperations())
        ->buildSorts($apiFiltersDTO->getSorts())
        ->buildPagination($apiFiltersDTO->getPage(), $apiFiltersDTO->getItemsPerPage())
        ->fetchArray();
    }

    public function findOneBy(int $id): ?Robot
    {
        return $this->serviceRepo()->findOneBy(['id' => $id]);
    }
}
