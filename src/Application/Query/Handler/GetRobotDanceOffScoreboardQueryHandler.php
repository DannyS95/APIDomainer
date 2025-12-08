<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotDanceOffScoreboardQuery;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Repository\RobotDanceOffHistoryRepositoryInterface;
use App\Infrastructure\Response\RobotDanceOffScoreboardResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetRobotDanceOffScoreboardQueryHandler
{
    public function __construct(private RobotDanceOffHistoryRepositoryInterface $historyRepository)
    {
    }

    /**
     * @return array<int, RobotDanceOffScoreboardResponse>
     */
    public function __invoke(GetRobotDanceOffScoreboardQuery $query): array
    {
        $year = $query->year() ?? (int) date('Y');
        $quarter = $query->quarter() ?? $this->currentQuarter();
        $page = max(1, $query->page() ?? 1);
        $perPage = min(100, max(1, $query->perPage() ?? 20));

        $histories = $this->historyRepository->findByPeriod(
            $year,
            $quarter,
            $page,
            $perPage
        );

        $scoreboard = [];

        foreach ($histories as $history) {
            $danceOffs = $history->getDanceOffs();

            if ($danceOffs->isEmpty()) {
                continue;
            }

            /** @var RobotDanceOff $latest */
            $latest = $danceOffs->first();

            if ($latest === false || $latest === null) {
                continue;
            }

            $scoreboard[] = new RobotDanceOffScoreboardResponse(
                $history->getId() ?? 0,
                $danceOffs->count(),
                $latest->getId() ?? 0,
                $latest->getCreatedAt(),
                $latest->getWinningTeam()?->getName(),
                $latest->getTeamOne()?->getName() ?? 'Team One',
                $latest->getTeamTwo()?->getName() ?? 'Team Two'
            );
        }

        return $scoreboard;
    }

    private function currentQuarter(): int
    {
        return (int) ceil((int) date('n') / 3);
    }
}
