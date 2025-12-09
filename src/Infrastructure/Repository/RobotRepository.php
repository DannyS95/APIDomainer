<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Infrastructure\Repository\Doctrine\DoctrineRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RobotRepository extends DoctrineRepository implements RobotRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private RobotQueryBuilder $robotQueryBuilder
    ) {
        parent::__construct($registry);
    }

    /**
     * Persist a Robot entity and save to the database.
     */
    public function save(Robot $robot): void
    {
        $this->persistEntity($robot);
    }

    /**
     * Find a Robot by ID.
     */
    public function findOneById(int $id): ?Robot
    {
        $entity = $this->findOneEntityById($id);

        if ($entity === null) {
            return null;
        }

        if (!$entity instanceof Robot) {
            throw new \LogicException(sprintf('Expected %s, got %s', Robot::class, get_debug_type($entity)));
        }

        return $entity;
    }

    /**
     * Remove a Robot from the database.
     */
    public function remove(Robot $robot): void
    {
        $this->removeEntity($robot);
    }

    protected function queryBuilder(): RobotQueryBuilder
    {
        return $this->robotQueryBuilder;
    }

    protected function entityClass(): string
    {
        return Robot::class;
    }

    protected function defaultSorts(): array
    {
        return ['id' => 'ASC'];
    }
}
