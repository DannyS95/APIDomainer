<?php

namespace App\Tests\Infrastructure\Repository;

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Entity\Team;
use App\Infrastructure\Repository\TeamQueryBuilder;
use App\Infrastructure\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TeamRepositoryTest extends TestCase
{
    public function testFindAllThrowsWhenOperatorIsNotAllowed(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $entityManager
            ->expects(self::once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $queryBuilder
            ->expects(self::once())
            ->method('select')
            ->with('t')
            ->willReturnSelf();

        $queryBuilder
            ->expects(self::once())
            ->method('from')
            ->with(Team::class, 't')
            ->willReturnSelf();

        $queryBuilder->expects(self::never())->method('andWhere');
        $queryBuilder->expects(self::never())->method('setParameter');

        $apiFilters = new ApiFiltersDTO(
            filters: ['name' => 'Team Rocket'],
            operations: ['name' => 'INVALID'],
            sorts: [],
            page: 1,
            itemsPerPage: 10
        );

        $teamQueryBuilder = new TeamQueryBuilder($entityManager);
        $repository = new TeamRepository($entityManager, $teamQueryBuilder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid operation: INVALID');

        $repository->findAll($apiFilters->toFilterCriteria());
    }
}
