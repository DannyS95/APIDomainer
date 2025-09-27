<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Team;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Doctrine\Repository\DoctrineComparisonFilterTrait;
use Doctrine\ORM\EntityManagerInterface;

class TeamRepository implements TeamRepositoryInterface
{
    use DoctrineComparisonFilterTrait;

    public function __construct(
        private EntityManagerInterface $entityManager
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
        $qb = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Team::class, 't');

        $this->applyFilters(
            $qb,
            $filterCriteria->getFilters(),
            $filterCriteria->getOperations(),
            't'
        );

        foreach ($filterCriteria->getSorts() as $field => $order) {
            $qb->addOrderBy("t.$field", $order);
        }

        $qb->setFirstResult(($filterCriteria->getPage() - 1) * $filterCriteria->getItemsPerPage())
           ->setMaxResults($filterCriteria->getItemsPerPage());

        return $qb->getQuery()->getResult();
    }

    public function delete(Team $team): void
    {
        $this->entityManager->remove($team);
        $this->entityManager->flush();
    }
}
