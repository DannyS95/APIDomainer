<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOff;
use Doctrine\ORM\QueryBuilder;

final class RobotDanceOffQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ENTITY = RobotDanceOff::class;
    private const ALIAS = 'rdo';
    private const TEAM_ONE_ALIAS = 'teamOne';
    private const TEAM_TWO_ALIAS = 'teamTwo';
    private const WINNER_ALIAS = 'winningTeam';

    public function fetchArray(): array
    {
        $qb = $this->getQueryBuilder();
        $results = $qb->getQuery()->getResult();
        $this->clearQueryBuilder();

        return $results;
    }

    protected function buildBaseQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS, self::TEAM_ONE_ALIAS, self::TEAM_TWO_ALIAS, self::WINNER_ALIAS)
            ->from(self::ENTITY, self::ALIAS)
            ->leftJoin(self::ALIAS . '.teamOne', self::TEAM_ONE_ALIAS)
            ->leftJoin(self::ALIAS . '.teamTwo', self::TEAM_TWO_ALIAS)
            ->leftJoin(self::ALIAS . '.winningTeam', self::WINNER_ALIAS);
    }

    protected function alias(): string
    {
        return self::ALIAS;
    }
}
