<?php

namespace App\Infrastructure\Handler;

use App\Domain\Service\RobotService;
use App\Domain\ValueObject\DanceOffTeams;
use App\Infrastructure\Request\RobotDanceOffRequest;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RobotDanceOffHandler
{
    private RobotService $robotService;

    public function __construct(RobotService $robotService) {
        $this->robotService = $robotService;
    }

    public function __invoke(RobotDanceOffRequest $request): void
    {
        $danceOffTeams = new DanceOffTeams($request->teamA, $request->teamB);

        $this->robotService->setRobotDanceOff($danceOffTeams);
    }
}
