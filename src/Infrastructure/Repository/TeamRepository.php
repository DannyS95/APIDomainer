<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Team;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use Doctrine\ORM\EntityManagerInterface;

class TeamRepository implements TeamRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamQueryBuilder $teamQueryBuilder
    ) {}

    public function save(Team $team): void
    {
        $this->entityManager->persist($team);
        $this->entityManager->flush();
    }

    public function findOneBy(int $id): ?Team
    {
        return $this->entityManager->getRepository(Team::class)->find($id);
    }

    public function findAll(FilterCriteria $filterCriteria): array
    {
        $queryBuilder = $this->teamQueryBuilder->create();

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

    public function delete(Team $team): void
    {
        $this->entityManager->remove($team);
        $this->entityManager->flush();
    }
}
