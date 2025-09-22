<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Team;
use App\Domain\Repository\TeamRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Application\DTO\ApiFiltersDTO;

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

    public function findAll(ApiFiltersDTO $apiFiltersDTO): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(Team::class, 't');

        $this->applyFilters(
            $qb,
            $apiFiltersDTO->getFilters(),
            $apiFiltersDTO->getOperations(),
            't'
        );

        foreach ($apiFiltersDTO->getSorts() as $field => $order) {
            $qb->addOrderBy("t.$field", $order);
        }

        $qb->setFirstResult(($apiFiltersDTO->getPage() - 1) * $apiFiltersDTO->getItemsPerPage())
           ->setMaxResults($apiFiltersDTO->getItemsPerPage());

        return $qb->getQuery()->getResult();
    }

    public function delete(Team $team): void
    {
        $this->entityManager->remove($team);
        $this->entityManager->flush();
    }
}
