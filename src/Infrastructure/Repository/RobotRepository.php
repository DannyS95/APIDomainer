<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Infrastructure\Doctrine\DoctrineRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RobotRepository extends DoctrineRepository implements RobotRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
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

    protected function entityClass(): string
    {
        return Robot::class;
    }
}
