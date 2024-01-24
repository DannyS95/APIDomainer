<?php

namespace App\Action;

use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Service\RobotService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotCollectionAction extends AbstractAction
{
    public function __construct(
        private RobotService $robotService
    ) {
    }

    public function __invoke(Request $request): ArrayCollection
    {
        parent::__construct($request);

        $apiFiltersDTO = new ApiFiltersDTO(
            filters: $this->filters(),
            operations: $this->operations(),
            sorts: $this->sorts(),
            page: $request->query->getInt('page', 1),
            itemsPerPage: $request->query->getInt('itemsPerPage', 10)
        );

        $models = $this->robotService->getRobots($apiFiltersDTO);

        return new ArrayCollection($models);
    }
}
