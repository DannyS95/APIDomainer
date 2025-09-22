<?php

namespace App\Action;

use App\Application\DTO\ApiFiltersDTO;
use App\Application\Query\GetRobotsQuery;
use App\Application\Query\Handler\GetRobotsQueryHandler;
use App\Application\Request\RequestDataMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotCollectionAction
{
    public function __construct(
        private GetRobotsQueryHandler $getRobotsQueryHandler,
        private RequestDataMapper $requestDataMapper,
    ) {
    }

    public function __invoke(Request $request): ArrayCollection
    {
        $filters = $this->requestDataMapper->getFilters();
        $operations = $this->requestDataMapper->getOperations();
        $sorts = $this->requestDataMapper->getSorts();

        $apiFiltersDTO = new ApiFiltersDTO(
            filters: $filters,
            operations: $operations,
            sorts: $sorts,
            page: $request->query->getInt('page', 1),
            itemsPerPage: $request->query->getInt('itemsPerPage', 10)
        );
    
        $query = new GetRobotsQuery($apiFiltersDTO);
        $models = $this->getRobotsQueryHandler->__invoke($query);

        return new ArrayCollection($models);
    }
}
