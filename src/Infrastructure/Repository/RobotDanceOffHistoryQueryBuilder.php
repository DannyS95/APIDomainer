<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOffHistory;
use App\Infrastructure\Doctrine\QueryBuilder\AbstractDoctrineQueryBuilder;
use Doctrine\ORM\QueryBuilder;
use DateTimeImmutable;

final class RobotDanceOffHistoryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ENTITY = RobotDanceOffHistory::class;
    private const ALIAS = 'battle';

    protected function buildBaseQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(self::ENTITY, self::ALIAS);
    }

    protected function alias(): string
    {
        return self::ALIAS;
    }

    public function withinQuarter(int $year, int $quarter): self
    {
        $sanitizedQuarter = max(1, min(4, $quarter));
        $startMonth = ($sanitizedQuarter - 1) * 3 + 1;
        $startDate = (new DateTimeImmutable())
            ->setDate($year, $startMonth, 1)
            ->setTime(0, 0, 0);
        $endDate = $startDate->modify('+3 months');

        $qb = $this->getQueryBuilder();
        $qb->andWhere(sprintf('%s.createdAt >= :start', self::ALIAS))
            ->andWhere(sprintf('%s.createdAt < :end', self::ALIAS))
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        return $this;
    }

    public function orderByMostRecent(): self
    {
        $this->getQueryBuilder()->orderBy(sprintf('%s.createdAt', self::ALIAS), 'DESC');

        return $this;
    }

    /**
     * @return array<int, RobotDanceOffHistory>
     */
    public function fetch(): array
    {
        $results = $this->fetchResult();

        return array_values(array_filter(
            $results,
            static fn (mixed $result): bool => $result instanceof RobotDanceOffHistory
        ));
    }
}
