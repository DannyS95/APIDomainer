<?php

namespace App\Action;

use App\Domain\Entity\RobotDanceOff;
use App\Domain\Repository\RobotBattleRepositoryInterface;
use App\Infrastructure\Response\RobotBattleScoreboardResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotBattleScoreboardAction
{
    public function __construct(private RobotBattleRepositoryInterface $robotBattleRepository)
    {
    }

    /**
     * @return array<int, RobotBattleScoreboardResponse>
     */
    public function __invoke(): array
    {
        $battles = $this->robotBattleRepository->findAll();
        $scoreboard = [];

        foreach ($battles as $battle) {
            $occurrences = $battle->getOccurrences()->toArray();

            if ($occurrences === []) {
                continue;
            }

            usort(
                $occurrences,
                static function (RobotDanceOff $left, RobotDanceOff $right): int {
                    return $left->getCreatedAt()->getTimestamp() <=> $right->getCreatedAt()->getTimestamp();
                }
            );

            /** @var RobotDanceOff $latest */
            $latest = end($occurrences);

            $scoreboard[] = new RobotBattleScoreboardResponse(
                $battle->getId() ?? 0,
                count($occurrences),
                $latest->getId() ?? 0,
                $latest->getCreatedAt(),
                $latest->getWinningTeam()?->getName(),
                $latest->getTeamOne()?->getName() ?? 'Team One',
                $latest->getTeamTwo()?->getName() ?? 'Team Two'
            );
        }

        return $scoreboard;
    }
}
