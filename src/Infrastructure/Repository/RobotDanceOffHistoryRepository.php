<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOffHistory;
use App\Domain\Repository\RobotDanceOffHistoryRepositoryInterface;
use App\Infrastructure\Doctrine\DoctrineReadRepository;
use DateTimeImmutable;

final class RobotDanceOffHistoryRepository extends DoctrineReadRepository implements RobotDanceOffHistoryRepositoryInterface
{
    private const ALIAS = 'battle';

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
        $sanitizedQuarter = max(1, min(4, $quarter));
        $startMonth = ($sanitizedQuarter - 1) * 3 + 1;
        $startDate = (new DateTimeImmutable())
            ->setDate($year, $startMonth, 1)
            ->setTime(0, 0, 0);
        $endDate = $startDate->modify('+3 months');

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(RobotDanceOffHistory::class, self::ALIAS)
            ->andWhere(sprintf('%s.createdAt >= :start', self::ALIAS))
            ->andWhere(sprintf('%s.createdAt < :end', self::ALIAS))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy(sprintf('%s.createdAt', self::ALIAS), 'DESC');

        $safePage = max(1, $page);
        $safePerPage = max(1, $perPage);
        $this->paginate($queryBuilder, $safePage, $safePerPage);

        $results = $queryBuilder->getQuery()->getResult();

        return array_values(array_filter(
            $results,
            static fn (mixed $result): bool => $result instanceof RobotDanceOffHistory
        ));
    }
}
