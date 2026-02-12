<?php

namespace App\Action;

use App\Application\Query\GetRobotDanceOffQuery;
use App\Application\Query\QueryBusDispatcher;
use App\Application\Request\RequestDataMapper;
use App\Responder\RobotDanceOffHistoryResponder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffHistoryAction
{
    public function __construct(
        private QueryBusDispatcher $queryBusDispatcher,
        private RequestDataMapper $requestDataMapper,
        private RobotDanceOffHistoryResponder $historyResponder
    ) {
    }

    /** @return list<\App\Infrastructure\Response\RobotDanceOffResponse> */
    public function __invoke(Request $request): array
    {
        $battleId = (int) $request->attributes->get('battleId', 0);

        if ($battleId <= 0) {
            throw new BadRequestHttpException('Battle ID must be a positive integer.');
        }

        $apiFiltersDTO = $this->requestDataMapper->toApiFiltersDTO(
            additionalFilters: ['battleId' => $battleId],
            additionalOperations: ['battleId' => 'eq'],
            itemsPerPageOverride: !$request->query->has('itemsPerPage') ? 0 : null
        );

        $query = new GetRobotDanceOffQuery($apiFiltersDTO);
        $danceOffs = $this->queryBusDispatcher->askArray($query);

        return $this->historyResponder->respond($danceOffs);
    }
}
