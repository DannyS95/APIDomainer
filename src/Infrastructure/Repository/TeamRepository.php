<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Team;
use App\Domain\Repository\TeamRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use App\Application\DTO\ApiFiltersDTO;

class TeamRepository implements TeamRepositoryInterface
{
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

        foreach ($apiFiltersDTO->getFilters() as $filter => $value) {
            $operator = $apiFiltersDTO->getOperations()[$filter] ?? '=';
            $qb->andWhere("t.$filter $operator :$filter")
               ->setParameter($filter, $value);
        }

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
