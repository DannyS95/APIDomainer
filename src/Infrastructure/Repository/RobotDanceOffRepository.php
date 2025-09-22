<?php

namespace App\Infrastructure\Repository;

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class RobotDanceOffRepository implements RobotDanceOffRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RobotDanceOffQueryBuilder $robotDanceOffQueryBuilder
    ) {}

    /**
     * Fetch all dance-offs with filters and sorting.
     */
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array
    {
        $queryBuilder = $this->robotDanceOffQueryBuilder->create();

        return $queryBuilder
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
     * Save a single dance-off.
     */
    public function save(RobotDanceOff $danceOff): void
    {
        $this->entityManager->persist($danceOff);
        $this->entityManager->flush();
    }

    /**
     * Find a single dance-off by its ID.
     */
    public function findOneBy(int $id): ?RobotDanceOff
    {
        return $this->entityManager->getRepository(RobotDanceOff::class)->find($id);
    }

    /**
     * Delete a single dance-off.
     */
    public function delete(RobotDanceOff $danceOff): void
    {
        $this->entityManager->remove($danceOff);
        $this->entityManager->flush();
    }

    /**
     * Bulk save multiple dance-offs.
     */
    public function bulkSave(array $robotDanceOffs): void
    {
        foreach ($robotDanceOffs as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }
}
