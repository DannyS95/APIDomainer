<?php

namespace App\Action;

use App\Domain\Service\RobotService;
use App\Application\DTO\ApiFiltersDTO;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Request\RequestDataMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotCollectionAction
{
    public function __construct(
        private RobotService $robotService,
        private RequestDataMapper $requestDataMapper
    ) {
    }

    public function __invoke(Request $request): ArrayCollection
    {
        $filters = $this->requestDataMapper->getFilters($request);
        $operations = $this->requestDataMapper->getOperations($request);
        $sorts = $this->requestDataMapper->getSorts($request);

        $apiFiltersDTO = new ApiFiltersDTO(
            filters: $filters,
            operations: $operations,
            sorts: $sorts,
            page: $request->query->getInt('page', 1),
            itemsPerPage: $request->query->getInt('itemsPerPage', 10)
        );

        $models = $this->robotService->getRobots($apiFiltersDTO);

        return new ArrayCollection($models);
    }
}
