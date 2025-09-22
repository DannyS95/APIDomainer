<?php

namespace App\Action;

use App\Application\DTO\ApiFiltersDTO;
use App\Application\Query\GetRobotDanceOffsQuery;
use App\Application\Query\Handler\GetRobotDanceOffsQueryHandler;
use App\Application\Request\RequestDataMapper;
use App\Responder\RobotDanceOffResponder;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffsCollectionAction
{
    public function __construct(
        private GetRobotDanceOffsQueryHandler $getRobotDanceOffsQueryHandler,
        private RequestDataMapper $requestDataMapper,
        private RobotDanceOffResponder $robotDanceOffResponder
    ) {
    }

    public function __invoke(): Collection
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

        $query = new GetRobotDanceOffsQuery($apiFiltersDTO);
        $models = $this->getRobotDanceOffsQueryHandler->__invoke($query);

        return $this->robotDanceOffResponder->respond($models);
    }
}
