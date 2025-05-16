<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Repository\RobotRepositoryInterface;

final class RobotRepository implements RobotRepositoryInterface
{
    public function __construct(
        private RobotQueryBuilder $robotQueryBuilder,
        private DoctrineRepositoryInterface $doctrineRepository
    ) {}

    /**
     * Fetch all robots with applied filters, sorts, and pagination.
     */
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array
    {
        return $this->robotQueryBuilder
            ->whereClauses(
                $apiFiltersDTO->getFilters(),
                $apiFiltersDTO->getOperations()
            )
            ->addSorts($apiFiltersDTO->getSorts())
            ->paginate(
                $apiFiltersDTO->getPage(),
                $apiFiltersDTO->getItemsPerPage()
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
     * Find a Robot by ID.
     */
    public function findOneBy(int $id): ?Robot
    {
        return $this->robotQueryBuilder
            ->whereId($id)
            ->fetchOne();
    }

    /**
     * Remove a Robot from the database.
     */
    public function remove(Robot $robot): void
    {
        $this->doctrineRepository->remove($robot);
        $this->doctrineRepository->save();
    }
}
