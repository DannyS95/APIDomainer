<?php

declare(strict_types=1);

namespace App\Tests\Application\Request;

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
}
