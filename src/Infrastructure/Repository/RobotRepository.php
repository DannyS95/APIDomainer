<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Doctrine\Repository\DoctrineRepositoryInterface;

final class RobotRepository implements RobotRepositoryInterface
{
    public function __construct(
        private RobotQueryBuilder $robotQueryBuilder,
        private DoctrineRepositoryInterface $doctrineRepository
    ) {}

    /**
     * Fetch all robots with applied filters, sorts, and pagination.
     */
    public function findAll(FilterCriteria $filterCriteria): array
    {
        $queryBuilder = $this->robotQueryBuilder->create();

        return $queryBuilder
            ->whereClauses(
                $filterCriteria->getFilters(),
                $filterCriteria->getOperations()
            )
            ->addSorts($filterCriteria->getSorts())
            ->paginate(
                $filterCriteria->getPage(),
                $filterCriteria->getItemsPerPage()
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
        $queryBuilder = $this->robotQueryBuilder->create();

        return $queryBuilder
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
