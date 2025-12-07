<?php

namespace App\Action;

use App\Domain\Entity\RobotDanceOff;
use App\Domain\Repository\RobotDanceOffHistoryRepositoryInterface;
use App\Infrastructure\Response\RobotDanceOffScoreboardResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffScoreboardAction
{
    public function __construct(private RobotDanceOffHistoryRepositoryInterface $robotBattleRepository)
    {
    }

    /**
     * @return array<int, RobotDanceOffScoreboardResponse>
     */
    public function __invoke(): array
    {
        $battles = $this->robotBattleRepository->findAll();
        $scoreboard = [];

        foreach ($battles as $battle) {
            $danceOffs = $battle->getDanceOffs()->toArray();

            if ($danceOffs === []) {
                continue;
            }

            usort(
                $danceOffs,
                static function (RobotDanceOff $left, RobotDanceOff $right): int {
                    return $left->getCreatedAt()->getTimestamp() <=> $right->getCreatedAt()->getTimestamp();
                }
            );

            /** @var RobotDanceOff $latest */
            $latest = end($danceOffs);

            $scoreboard[] = new RobotDanceOffScoreboardResponse(
                $battle->getId() ?? 0,
                count($danceOffs),
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
