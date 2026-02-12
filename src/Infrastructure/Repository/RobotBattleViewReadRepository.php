<?php

namespace App\Infrastructure\Repository;

use App\Domain\ReadModel\RobotBattleView;
use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\Repository\RobotBattleViewReadRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Doctrine\DoctrineReadRepository;
use App\Infrastructure\Repository\Exception\UnexpectedQueryResultException;

final class RobotBattleViewReadRepository extends DoctrineReadRepository implements RobotBattleViewReadRepositoryInterface
{
    private const ALIAS = 'rbv';
    private const DEFAULT_ITEMS_PER_PAGE = 50;
    private const MAX_ITEMS_PER_PAGE = 100;

    /**
     * @var array<string, string>
     */
    private const DEFAULT_SORTS = ['createdAt' => 'DESC'];

    /**
     * @return list<RobotBattleViewInterface>
     */
    public function findByCriteria(FilterCriteria $filterCriteria): array
    {
        [$page, $itemsPerPage, $sorts] = $this->resolvePaginationAndSorts(
            $filterCriteria,
            self::DEFAULT_SORTS,
            self::DEFAULT_ITEMS_PER_PAGE,
            self::MAX_ITEMS_PER_PAGE
        );

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(RobotBattleView::class, self::ALIAS);

        $this->applyFilters(
            $queryBuilder,
            $filterCriteria->getFilters(),
            $filterCriteria->getOperations(),
            self::ALIAS
        );
        $this->applySorts($queryBuilder, $sorts, self::ALIAS);
        $this->paginate($queryBuilder, $page, $itemsPerPage);

        $results = $queryBuilder->getQuery()->getResult();

        return array_map(static function (mixed $result): RobotBattleViewInterface {
            if ($result instanceof RobotBattleViewInterface) {
                return $result;
            }

            if (is_array($result)) {
                $entity = $result[self::ALIAS] ?? $result[0] ?? null;
                if ($entity instanceof RobotBattleViewInterface) {
                    return $entity;
                }
            }

            throw UnexpectedQueryResultException::forRobotBattleView($result);
        }, $results);
    }
}
