<?php

namespace App\Infrastructure\Handler;

use App\Domain\Service\RobotService;
use App\Domain\ValueObject\DanceOffTeams;
use App\Infrastructure\Request\RobotDanceOffRequest;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RobotDanceOffHandler
{
    public function __construct(private readonly RobotService $robotService)
    {
    }

    public function __invoke(RobotDanceOffRequest $request): void
    {
        $danceOffTeams = new DanceOffTeams($request->teamA, $request->teamB);

        $this->robotService->setRobotDanceOff($danceOffTeams);
    }
}
