<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotDanceOff;
use Doctrine\ORM\QueryBuilder;
use UnexpectedValueException;

final class RobotDanceOffQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ENTITY = RobotDanceOff::class;
    private const ALIAS = 'rdo';
    private const TEAM_ONE_ALIAS = 'teamOne';
    private const TEAM_TWO_ALIAS = 'teamTwo';
    private const WINNING_TEAM_ALIAS = 'winningTeam';

    protected function buildBaseQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS, self::TEAM_ONE_ALIAS, self::TEAM_TWO_ALIAS, self::WINNING_TEAM_ALIAS)
            ->from(self::ENTITY, self::ALIAS)
            ->leftJoin(sprintf('%s.teamOne', self::ALIAS), self::TEAM_ONE_ALIAS)
            ->leftJoin(sprintf('%s.teamTwo', self::ALIAS), self::TEAM_TWO_ALIAS)
            ->leftJoin(sprintf('%s.winningTeam', self::ALIAS), self::WINNING_TEAM_ALIAS);
    }

    protected function alias(): string
    {
        return self::ALIAS;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchArray(): array
    {
        return $this->fetchArrayResult();
    }

    /**
     * @return array<int, RobotDanceOff>
     */
    public function fetch(): array
    {
        $results = $this->fetchResult();

        return array_map(function (mixed $row): RobotDanceOff {
            if ($row instanceof RobotDanceOff) {
                return $row;
            }

            if (is_array($row)) {
                $byAlias = $row[self::ALIAS] ?? null;
                if ($byAlias instanceof RobotDanceOff) {
                    return $byAlias;
                }

                $first = $row[0] ?? null;
                if ($first instanceof RobotDanceOff) {
                    return $first;
                }

                foreach ($row as $value) {
                    if ($value instanceof RobotDanceOff) {
                        return $value;
                    }
                }
            }

            throw new UnexpectedValueException('Expected RobotDanceOff instance from query result.');
        }, $results);
    }
}
