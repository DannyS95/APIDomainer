<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOffHistory;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Infrastructure\Doctrine\DoctrineRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RobotDanceOffRepository extends DoctrineRepository implements RobotDanceOffRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private RobotBattleViewQueryBuilder $robotBattleViewQueryBuilder
    ) {
        parent::__construct($registry);
    }

    /**
     * Save a single dance-off.
     */
    public function save(RobotDanceOff $danceOff): void # these mthods will repeat
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
     */
    public function bulkSave(array $RobotDanceOff): void
    {
        $this->persistEntities($RobotDanceOff);
    }

    public function findLatestByBattle(RobotDanceOffHistory $battle): ?RobotDanceOff
    {
        return $this->entityManager->getRepository(RobotDanceOff::class)
            ->createQueryBuilder('rdo')
            ->where('rdo.battle = :battle')
            ->setParameter('battle', $battle)
            ->orderBy('rdo.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    protected function queryBuilder(): RobotBattleViewQueryBuilder
    {
        return $this->robotBattleViewQueryBuilder;
    }

    protected function defaultSorts(): array
    {
        return ['createdAt' => 'DESC'];
    }

    protected function defaultItemsPerPage(): int
    {
        return 50;
    }

    protected function maxItemsPerPage(): int
    {
        return 100;
    }

    protected function entityClass(): string
    {
        return RobotDanceOff::class;
    }
}
