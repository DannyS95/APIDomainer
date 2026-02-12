<?php

namespace App\Action;

use App\Application\Query\GetRobotDanceOffScoreboardQuery;
use App\Application\Query\QueryBusDispatcher;
use App\Application\Request\RequestDataMapper;
use App\Responder\RobotDanceOffScoreboardResponder;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffScoreboardAction
{
    public function __construct(
        private QueryBusDispatcher $queryBusDispatcher,
        private RequestDataMapper $requestDataMapper,
        private RobotDanceOffScoreboardResponder $scoreboardResponder
    ) {
    }

    public function __invoke()
    {
        $filters = $this->requestDataMapper->getFilters();
        $pagination = $this->requestDataMapper->getPagination();

        $query = new GetRobotDanceOffScoreboardQuery(
            $filters['year'] ?? null,
            $filters['quarter'] ?? null,
            $pagination['page'] ?? null,
            $pagination['itemsPerPage'] ?? null
        );

        $result = $this->queryBusDispatcher->askArray($query);

        return $this->scoreboardResponder->respond($result);
    }
}
