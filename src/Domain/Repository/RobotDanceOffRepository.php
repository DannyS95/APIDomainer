<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotDanceOff;
use App\Application\DTO\ApiFiltersDTO;
use App\Infrastructure\Repository\DoctrineRepositoryInterface;

final class RobotDanceOffRepository implements RobotDanceOffRepositoryInterface
{
    private const ENTITY = RobotDanceOff::class;
    private const ALIAS = 'robotDanceOff';

    public function __construct(
        private DoctrineRepositoryInterface $doctrineRepository
    ) {}

    public function findAll(ApiFiltersDTO $apiFiltersDTO): array
    {
        return $this->doctrineRepository
            ->createQueryBuilder(self::ENTITY, self::ALIAS)
            ->buildClauses($apiFiltersDTO->getFilters(), $apiFiltersDTO->getOperations())
            ->buildSorts($apiFiltersDTO->getSorts())
            ->buildPagination($apiFiltersDTO->getPage(), $apiFiltersDTO->getItemsPerPage())
            ->fetchArray();
    }

    public function bulkSave(array $robotDanceOff): void
    {
        foreach ($robotDanceOff as $entity) {
            $this->doctrineRepository->persist($entity);
        }
        $this->doctrineRepository->save();
    }
}
