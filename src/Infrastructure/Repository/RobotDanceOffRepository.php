<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOffHistory;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Infrastructure\Doctrine\DoctrineRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RobotDanceOffRepository extends DoctrineRepository implements RobotDanceOffRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    /**
     * Save a single dance-off.
     */
    public function save(RobotDanceOff $danceOff): void
    {
        $this->persistEntity($danceOff);
    }

    /**
     * Find a single dance-off by its ID.
     */
    public function findOneById(int $id): ?RobotDanceOff
    {
        $entity = $this->findOneEntityById($id);

        if ($entity === null) {
            return null;
        }

        if (!$entity instanceof RobotDanceOff) {
            throw new \LogicException(sprintf('Expected %s, got %s', RobotDanceOff::class, get_debug_type($entity)));
        }

        return $entity;
    }

    /**
     * Bulk save multiple dance-offs.
     *
     * @param array<int, RobotDanceOff> $danceOffs
     */
    public function bulkSave(array $danceOffs): void
    {
        $this->persistEntities($danceOffs);
    }

    public function findLatestByBattle(RobotDanceOffHistory $battle): ?RobotDanceOff
    {
        $result = $this->createQueryBuilder('rdo')
            ->where('rdo.battle = :battle')
            ->setParameter('battle', $battle)
            ->orderBy('rdo.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        if (!$result instanceof RobotDanceOff) {
            throw new \LogicException(sprintf('Expected %s, got %s', RobotDanceOff::class, get_debug_type($result)));
        }

        return $result;
    }

    protected function entityClass(): string
    {
        return RobotDanceOff::class;
    }
}
