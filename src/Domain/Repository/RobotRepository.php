<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Robot;
use App\Application\DTO\ApiFiltersDTO;
use App\Infrastructure\Repository\DoctrineRepositoryInterface;

final class RobotRepository implements RobotRepositoryInterface
{
    private const ENTITY = Robot::class;
    private const ALIAS = 'robot';

    public function __construct(
        private DoctrineRepositoryInterface $doctrineRepository
    ) {}

    /**
     * Fetch all robots with applied filters, sorts, and pagination.
     */
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array
    {
        // 🏷️ Build the query with fluent methods
        return $this->doctrineRepository
            ->createQueryBuilder(self::ENTITY, self::ALIAS)
            ->buildClauses(
                filters: $apiFiltersDTO->getFilters(),
                operations: $apiFiltersDTO->getOperations()
            )
            ->buildSorts($apiFiltersDTO->getSorts())
            ->buildPagination(
                page: $apiFiltersDTO->getPage(),
                itemsPerPage: $apiFiltersDTO->getItemsPerPage()
            )
            ->fetchArray();
    }

    /**
     * Persist a Robot entity and save to the database.
     */
    public function save(Robot $robot): void
    {
        $this->doctrineRepository->persist($robot);
        $this->doctrineRepository->save();
    }

    /**
     * Find a Robot by its ID.
     */
    public function findOneBy(int $id): ?Robot
    {
        return $this->doctrineRepository
            ->serviceRepo()
            ->findOneBy(['id' => $id]);
    }

    /**
     * Remove a Robot from the database.
     */
    public function remove(Robot $robot): void
    {
        $this->doctrineRepository->serviceRepo()->remove($robot);
        $this->doctrineRepository->save();
    }
}
