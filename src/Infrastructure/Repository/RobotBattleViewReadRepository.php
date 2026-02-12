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
     * API-facing field aliases mapped to Doctrine property names.
     *
     * @var array<string, string>
     */
    private const FIELD_ALIASES = ['id' => 'battleReplayId'];

    /**
     * @return list<RobotBattleViewInterface>
     */
    public function findByCriteria(FilterCriteria $filterCriteria): array
    {
        [$page, $itemsPerPage, $requestedSorts] = $this->resolvePaginationAndSorts(
            $filterCriteria,
            self::DEFAULT_SORTS,
            self::DEFAULT_ITEMS_PER_PAGE,
            self::MAX_ITEMS_PER_PAGE
        );

        $filters = $this->normalizeFields($filterCriteria->getFilters());
        $operations = $this->normalizeFields($filterCriteria->getOperations());
        $sorts = $this->normalizeFields($requestedSorts);

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(RobotBattleView::class, self::ALIAS);

        $this->applyFilters(
            $queryBuilder,
            $filters,
            $operations,
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

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private function normalizeFields(array $values): array
    {
        $normalized = [];

        foreach ($values as $field => $value) {
            $resolvedField = self::FIELD_ALIASES[$field] ?? $field;

            // Keep explicit canonical fields when both alias and canonical are provided.
            if ($resolvedField !== $field && array_key_exists($resolvedField, $values)) {
                continue;
            }

            $normalized[$resolvedField] = $value;
        }

        return $normalized;
    }
}
