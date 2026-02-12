<?php

namespace App\Infrastructure\Handler;

use App\Domain\Service\RobotService;
use App\Domain\ValueObject\BattleReplayInstruction;
use App\Domain\ValueObject\RobotReplacement;
use App\Infrastructure\Request\RobotBattleReplayRequest;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RobotDanceOffHistoryReplayHandler
{
    public function __construct(private readonly RobotService $robotService)
    {
    }

    public function __invoke(RobotBattleReplayRequest $request): void
    {
        $instruction = new BattleReplayInstruction(
            $request->battleId,
            $this->mapReplacements($request->teamAReplacements),
            $this->mapReplacements($request->teamBReplacements)
        );

        $this->robotService->replayRobotBattle($instruction);
    }

    /**
     * @param array<int, array{out: int, in: int}> $payload
     * @return array<int, RobotReplacement>
     */
    private function mapReplacements(array $payload): array
    {
        return array_map(
            static fn (array $replacement): RobotReplacement => new RobotReplacement(
                $replacement['out'],
                $replacement['in']
            ),
            $payload
        );
    }
}
