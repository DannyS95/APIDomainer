<?php

namespace App\Action;

use App\Domain\RobotService;
use App\Action\AbstractAction;
use App\Infrastructure\DTO\ApiFiltersDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotCollectionAction extends AbstractAction
{
    public function __construct(private RobotService $robotService)
    {
    }

    public function __invoke(Request $request): void
    {
        parent::__construct(request: $request);

        $filters = $this->filters();

        $operations = $this->operations();

        $page = $request->query->get('page');

        $itemsPerPage = $request->query->get('itemsPerPage');

        $apiFiltersDTO = new ApiFiltersDTO(filters: $filters, operations: $operations, page: $page, itemsPerPage: $itemsPerPage);

        $models = $this->robotService->getRobots($apiFiltersDTO);
    }
}
