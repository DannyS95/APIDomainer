<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\Doctrine\QueryBuilder\AbstractDoctrineQueryBuilder;
use App\Infrastructure\Doctrine\View\RobotBattleView;
use App\Infrastructure\Repository\Exception\UnexpectedQueryResultException;
use Doctrine\ORM\QueryBuilder;

final class RobotBattleViewQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ENTITY = RobotBattleView::class;
    private const ALIAS = 'rbv';

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

    /**
     * @return array<int, RobotBattleView>
     */
    public function fetch(): array
    {
        $results = $this->fetchResult();

        return array_map(static function (mixed $result): RobotBattleView {
            if ($result instanceof RobotBattleView) {
                return $result;
            }

            if (is_array($result)) {
                $entity = $result[self::ALIAS] ?? $result[0] ?? null;
                if ($entity instanceof RobotBattleView) {
                    return $entity;
                }
            }

            throw UnexpectedQueryResultException::forRobotBattleView($result);
        }, $results);
    }
}
