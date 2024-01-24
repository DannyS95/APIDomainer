<?php

namespace App\Action;

use App\Action\AbstractAction;
use App\Domain\Service\RobotService;
use App\Application\DTO\ApiFiltersDTO;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffsCollectionAction extends AbstractAction
{
    public function __construct(private RobotService $robotService)
    {
    }

    public function __invoke(Request $request): Collection
    {
        parent::__construct(request: $request);

        $apiFiltersDTO = new ApiFiltersDTO(
            filters: $this->filters(),
            operations: $this->operations(),
            sorts: $this->sorts(),
            page: $request->query->getInt('page', 1),
            itemsPerPage: $request->query->getInt('itemsPerPage', 10)
        );

        $models = $this->robotService->getRobotDanceOffs($apiFiltersDTO);

        return new ArrayCollection($models);
    }
}
