<?php

namespace App\Action;

use App\Application\Query\GetRobotsQuery;
use App\Application\Query\QueryBusDispatcher;
use App\Application\Request\RequestDataMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotCollectionAction
{
    public function __construct(
        private QueryBusDispatcher $queryBusDispatcher,
        private RequestDataMapper $requestDataMapper,
    ) {
    }

    public function __invoke(): ArrayCollection
    {
        $query = new GetRobotsQuery($this->requestDataMapper->toApiFiltersDTO());
        $models = $this->queryBusDispatcher->askArray($query);

        return new ArrayCollection($models);
    }
}
