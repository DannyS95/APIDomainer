<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotQuery;
use App\Domain\Entity\Robot;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Domain\Service\RobotServiceException;
use App\Domain\Service\RobotValidatorService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetRobotQueryHandler
{
    public function __construct(
        private readonly RobotRepositoryInterface $robotRepository,
        private readonly RobotValidatorService $robotValidatorService
    ) {
    }

    public function __invoke(GetRobotQuery $query): Robot
    {
        $robotId = $query->getRobotId();
        $this->robotValidatorService->validateRobotIds([$robotId]);

        $robot = $this->robotRepository->findOneById($robotId);

        if ($robot === null) {
            throw new RobotServiceException(sprintf('Robot ID %d does not exist.', $robotId));
        }

        return $robot;
    }
}
