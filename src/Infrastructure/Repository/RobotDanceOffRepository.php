<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOff;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use Doctrine\ORM\EntityManagerInterface;

final class RobotDanceOffRepository implements RobotDanceOffRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RobotDanceOffQueryBuilder $robotDanceOffQueryBuilder
    ) {}

    /**
     * Fetch all dance-offs with filters and sorting.
     *
     * @return array<int, RobotDanceOff>
     */
    public function findAll(FilterCriteria $filterCriteria): array
    {
        $queryBuilder = $this->robotDanceOffQueryBuilder->create();

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
            ->fetch();
    }

    /**
     * Save a single dance-off.
     */
    public function save(RobotDanceOff $danceOff): void # these mthods will repeat
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
    public function bulkSave(array $RobotDanceOff): void
    {
        foreach ($RobotDanceOff as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }
}
