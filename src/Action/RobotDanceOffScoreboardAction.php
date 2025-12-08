<?php

namespace App\Action;

use App\Application\Query\GetRobotDanceOffScoreboardQuery;
use App\Application\Request\RequestDataMapper;
use App\Responder\RobotDanceOffScoreboardResponder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use RuntimeException;

#[AsController]
final class RobotDanceOffScoreboardAction
{
    public function __construct(
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $queryBus,
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

        $envelope = $this->queryBus->dispatch($query);
        $handled = $envelope->last(HandledStamp::class);

        if (!$handled instanceof HandledStamp) {
            throw new RuntimeException('No handler returned a result for GetRobotDanceOffScoreboardQuery.');
        }

        $result = $handled->getResult();

        if (!is_array($result)) {
            throw new RuntimeException('Unexpected response type for GetRobotDanceOffScoreboardQuery.');
        }

        return $this->scoreboardResponder->respond($result);
    }

    private function currentQuarter(): int
    {
        return (int) ceil((int) date('n') / 3);
    }
}
