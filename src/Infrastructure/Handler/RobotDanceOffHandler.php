<?php

namespace App\Infrastructure\Handler;

use App\Domain\Service\RobotService;
use App\Infrastructure\Request\RobotDanceOffRequest;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RobotDanceOffHandler
{
    private RobotService $robotService;

    public function __construct(RobotService $robotService) {
        $this->robotService = $robotService;
    }

    public function __invoke(RobotDanceOffRequest $request)
    {
        $this->robotService->setRobotDanceOff($request);
    }
}
