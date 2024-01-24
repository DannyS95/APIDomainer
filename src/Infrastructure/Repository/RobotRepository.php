<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\DTO\ApiFiltersDTO;
use App\Domain\Entity\Robot;
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
        $self = $this;

        $self->buildClauses($apiFiltersDTO);

        $self->buildSorts($apiFiltersDTO);

        $self->buildPagination($apiFiltersDTO);

        return $self->fetchArray();
    }
}
