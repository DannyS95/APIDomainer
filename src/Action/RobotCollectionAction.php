<?php

namespace App\Action;

use App\Domain\RobotService;
use App\Action\AbstractAction;
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

        $models = $this->robotService->getRobots(page: $page, itemsPerPage: $itemsPerPage, filters: $filters, operations: $operations);
    }
}
