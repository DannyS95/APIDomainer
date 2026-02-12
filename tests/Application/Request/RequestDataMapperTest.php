<?php

declare(strict_types=1);

namespace App\Tests\Application\Request;

use App\Application\DTO\ApiFiltersDTO;
use App\Application\Request\RequestDataMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestDataMapperTest extends TestCase
{
    public function testItParsesFiltersFromQueryParameters(): void
    {
        $requestStack = new RequestStack();
        $request = Request::create(
            '/robots',
            'GET',
            [
                'filter' => ['name' => ['lk' => 'Bender']],
                'id' => ['eq' => '2'],
                'orderBy' => ['name' => 'ASC'],
                'page' => '3',
                'itemsPerPage' => '25',
            ]
        );

        $requestStack->push($request);

        $requestDataMapper = new RequestDataMapper($requestStack);

        self::assertEquals(
            ['name' => 'Bender', 'id' => '2'],
            $requestDataMapper->getFilters()
        );

        self::assertEquals(
            ['name' => 'lk', 'id' => 'eq'],
            $requestDataMapper->getOperations()
        );
    }

    public function testItDefaultsOperationToEqForScalarFilters(): void
    {
        $requestStack = new RequestStack();
        $request = Request::create(
            '/robots',
            'GET',
            [
                'experience' => '42',
            ]
        );

        $requestStack->push($request);

        $requestDataMapper = new RequestDataMapper($requestStack);

        self::assertEquals(['experience' => '42'], $requestDataMapper->getFilters());
        self::assertEquals(['experience' => 'eq'], $requestDataMapper->getOperations());
    }

    public function testItKeepsZeroValues(): void
    {
        $requestStack = new RequestStack();
        $request = Request::create(
            '/robots',
            'GET',
            [
                'id' => ['eq' => '0'],
            ]
        );

        $requestStack->push($request);

        $requestDataMapper = new RequestDataMapper($requestStack);

        self::assertEquals(['id' => '0'], $requestDataMapper->getFilters());
        self::assertEquals(['id' => 'eq'], $requestDataMapper->getOperations());
    }

    public function testItRefreshesParametersForSubsequentRequests(): void
    {
        $requestStack = new RequestStack();

        $firstRequest = Request::create('/robots', 'GET');
        $requestStack->push($firstRequest);

        $requestDataMapper = new RequestDataMapper($requestStack);

        self::assertSame([], $requestDataMapper->getFilters());
        self::assertSame([], $requestDataMapper->getOperations());

        $requestStack->pop();

        $secondRequest = Request::create(
            '/robots',
            'GET',
            [
                'id' => ['eq' => '2'],
            ]
        );

        $requestStack->push($secondRequest);

        self::assertEquals(['id' => '2'], $requestDataMapper->getFilters());
        self::assertEquals(['id' => 'eq'], $requestDataMapper->getOperations());
    }

    public function testToApiFiltersDtoBuildsMergedCriteriaWithPaginationOverride(): void
    {
        $requestStack = new RequestStack();
        $request = Request::create(
            '/robot-battles/7/dance-offs',
            'GET',
            [
                'filter' => ['name' => ['lk' => 'Atlas']],
                'page' => '2',
                'itemsPerPage' => '25',
            ]
        );

        $requestStack->push($request);

        $requestDataMapper = new RequestDataMapper($requestStack);
        $dto = $requestDataMapper->toApiFiltersDTO(
            additionalFilters: ['battleId' => 7],
            additionalOperations: ['battleId' => 'eq'],
            itemsPerPageOverride: 0
        );

        self::assertInstanceOf(ApiFiltersDTO::class, $dto);
        self::assertEquals(['name' => 'Atlas', 'battleId' => 7], $dto->getFilters());
        self::assertEquals(['name' => 'lk', 'battleId' => 'eq'], $dto->getOperations());
        self::assertSame(2, $dto->getPage());
        self::assertSame(0, $dto->getItemsPerPage());
    }
}
