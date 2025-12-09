<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOffHistory;
use App\Domain\Repository\RobotDanceOffHistoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class RobotDanceOffHistoryRepository implements RobotDanceOffHistoryRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RobotDanceOffHistoryQueryBuilder $historyQueryBuilder
    ) {
    }

    public function findOneById(int $id): ?RobotDanceOffHistory
    {
        return $this->entityManager->getRepository(RobotDanceOffHistory::class)->find($id);
    }

    public function save(RobotDanceOffHistory $battle): void
    {
        $this->entityManager->persist($battle);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(RobotDanceOffHistory::class)->findAll();
    }

    public function findByPeriod(int $year, int $quarter, int $page, int $perPage): array
    {
        return $this->historyQueryBuilder
            ->create()
            ->withinQuarter($year, $quarter)
            ->orderByMostRecent()
            ->paginate($page, $perPage)
            ->fetch();
    }
}
