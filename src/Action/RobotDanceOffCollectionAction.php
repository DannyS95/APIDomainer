<?php

namespace App\Action;

use App\Application\DTO\ApiFiltersDTO;
use App\Application\Query\GetRobotDanceOffQuery;
use App\Application\Request\RequestDataMapper;
use App\Responder\RobotDanceOffResponder;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsController]
final class RobotDanceOffCollectionAction
{
    public function __construct(
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $queryBus,
        private RequestDataMapper $requestDataMapper,
        private RobotDanceOffResponder $robotDanceOffResponder
    ) {
    }

    /** @return list<\App\Infrastructure\Response\RobotDanceOffResponse> */
    public function __invoke(): array
    {
        $filters = $this->requestDataMapper->getFilters();
        $operations = $this->requestDataMapper->getOperations();
        $sorts = $this->requestDataMapper->getSorts();
        $pagination = $this->requestDataMapper->getPagination();

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

        $models = $handledStamp->getResult();

        return $this->robotDanceOffResponder->respond($models);
    }
}
