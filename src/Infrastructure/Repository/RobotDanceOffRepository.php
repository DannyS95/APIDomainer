<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\Entity\RobotDanceOff;

final class RobotDanceOffRepository implements RobotDanceOffRepositoryInterface
{
    public function __construct(
        private RobotDanceOffQueryBuilder $robotDanceOffQueryBuilder,
        private DoctrineRepositoryInterface $doctrineRepository
    ) {}

    /**
     * Fetch all dance-offs with filters and sorting.
     */
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array
    {
        return $this->robotDanceOffQueryBuilder
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
     * Bulk save multiple dance-offs.
     */
    public function bulkSave(array $robotDanceOffs): void
    {
        foreach ($robotDanceOffs as $entity) {
            $this->doctrineRepository->persist($entity);
        }
        $this->doctrineRepository->save();
    }
}
