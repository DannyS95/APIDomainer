<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Team;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Infrastructure\Repository\Doctrine\DoctrineRepository;
use Doctrine\ORM\EntityManagerInterface;

class TeamRepository extends DoctrineRepository implements TeamRepositoryInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private TeamQueryBuilder $teamQueryBuilder
    ) {
        parent::__construct($entityManager);
    }

    public function save(Team $team): void
    {
        $this->persistEntity($team);
    }

    public function findOneBy(int $id): ?Team
    {
        $entity = $this->findOneEntityById($id);

        if ($entity === null) {
            return null;
        }

        if (!$entity instanceof Team) {
            throw new \LogicException(sprintf('Expected %s, got %s', Team::class, get_debug_type($entity)));
        }

        return $entity;
    }

    protected function queryBuilder(): TeamQueryBuilder
    {
        return $this->teamQueryBuilder;
    }

    protected function entityClass(): string
    {
        return Team::class;
    }

    protected function defaultSorts(): array
    {
        return ['id' => 'ASC'];
    }
}
