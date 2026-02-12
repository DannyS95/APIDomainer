<?php

namespace App\Action;

use App\Application\Query\GetRobotDanceOffQuery;
use App\Application\Query\QueryBusDispatcher;
use App\Application\Request\RequestDataMapper;
use App\Responder\RobotDanceOffResponder;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffCollectionAction
{
    public function __construct(
        private QueryBusDispatcher $queryBusDispatcher,
        private RequestDataMapper $requestDataMapper,
        private RobotDanceOffResponder $robotDanceOffResponder
    ) {
    }

    /** @return list<\App\Infrastructure\Response\RobotDanceOffResponse> */
    public function __invoke(): array
    {
        $query = new GetRobotDanceOffQuery($this->requestDataMapper->toApiFiltersDTO());
        $models = $this->queryBusDispatcher->askArray($query);

        return $this->robotDanceOffResponder->respond($models);
    }
}
