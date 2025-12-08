<?php

namespace App\Action;

use App\Application\DTO\ApiFiltersDTO;
use App\Application\Query\GetRobotDanceOffQuery;
use App\Application\Request\RequestDataMapper;
use App\Responder\RobotDanceOffHistoryResponder;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsController]
final class RobotDanceOffHistoryAction
{
    public function __construct(
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $queryBus,
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

        $filters = $this->requestDataMapper->getFilters();
        $operations = $this->requestDataMapper->getOperations();
        $sorts = $this->requestDataMapper->getSorts();
        $pagination = $this->requestDataMapper->getPagination();

        $filters['battleId'] = $battleId;

        if (!$request->query->has('itemsPerPage')) {
            $pagination['itemsPerPage'] = 0;
        }

        $apiFiltersDTO = new ApiFiltersDTO(
            filters: $filters,
            operations: $operations,
            sorts: $sorts,
            page: $pagination['page'],
            itemsPerPage: $pagination['itemsPerPage']
        );

        $query = new GetRobotDanceOffQuery($apiFiltersDTO);
        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp instanceof HandledStamp) {
            throw new RuntimeException('No handler returned a result for GetRobotDanceOffQuery.');
        }

        $danceOffs = $handledStamp->getResult();

        return $this->historyResponder->respond($danceOffs);
    }
}
