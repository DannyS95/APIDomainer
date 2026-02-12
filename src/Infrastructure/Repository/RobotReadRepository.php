<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use App\Domain\Repository\RobotReadRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Doctrine\DoctrineReadRepository;

final class RobotReadRepository extends DoctrineReadRepository implements RobotReadRepositoryInterface
{
    private const ALIAS = 'r';
    private const DEFAULT_ITEMS_PER_PAGE = 50;
    private const MAX_ITEMS_PER_PAGE = 100;

    /**
     * @var array<string, string>
     */
    private const DEFAULT_SORTS = ['id' => 'ASC'];

    /**
     * @return array<int, array<string, mixed>>
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
            ->from(Robot::class, self::ALIAS);

        $this->applyFilters(
            $queryBuilder,
            $filterCriteria->getFilters(),
            $filterCriteria->getOperations(),
            self::ALIAS
        );
        $this->applySorts($queryBuilder, $sorts, self::ALIAS);
        $this->paginate($queryBuilder, $page, $itemsPerPage);

        /** @var array<int, array<string, mixed>> $results */
        $results = $queryBuilder->getQuery()->getArrayResult();

        return $results;
    }
}
