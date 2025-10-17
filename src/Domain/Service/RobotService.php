<?php

namespace App\Domain\Service;

use RobotServiceException;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotBattle;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\Team;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\Repository\RobotBattleRepositoryInterface;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Domain\ValueObject\BattleReplayInstruction;
use App\Domain\ValueObject\DanceOffTeams;
use App\Domain\ValueObject\RobotReplacement;

final class RobotService
{
    public function __construct(
        private RobotRepositoryInterface $robotRepository,
        private RobotDanceOffRepositoryInterface $robotDanceOffRepository,
        private RobotBattleRepositoryInterface $robotBattleRepository,
        private TeamRepositoryInterface $teamRepository,
        private RobotValidatorService $robotValidatorService
    ) {}

    /**
     * Set a dance off between two teams of robots.
     */
    public function setRobotDanceOff(DanceOffTeams $danceOffTeams): void
    {
        $robotIds = $danceOffTeams->allRobotIds();

        $this->robotValidatorService->validateRobotIds($robotIds);

        $battle = $this->resolveBattle(null);

        // Create the teams and associate robots
        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        foreach ($danceOffTeams->teamOneRobotIds() as $robotId) {
            $teamOne->addRobot($this->loadRobot($robotId));
        }

        foreach ($danceOffTeams->teamTwoRobotIds() as $robotId) {
            $teamTwo->addRobot($this->loadRobot($robotId));
        }

        // Persist teams
        $this->teamRepository->save($teamOne);
        $this->teamRepository->save($teamTwo);

        // Create the DanceOff and set relationships
        $danceOff = new RobotDanceOff();
        $battle->addOccurrence($danceOff);
        $danceOff->setTeamOne($teamOne);
        $danceOff->setTeamTwo($teamTwo);
        $teamOne->setDanceOff($danceOff);
        $teamTwo->setDanceOff($danceOff);

        // Calculate and set the winning team
        $winningTeam = $this->calculateWinningTeam($teamOne, $teamTwo);
        $danceOff->setWinningTeam($winningTeam);

        // Persist the DanceOff
        $this->robotDanceOffRepository->save($danceOff);
    }

    /**
     * Determines the winning team based on individual battles.
     */
    private function calculateWinningTeam(Team $teamOne, Team $teamTwo): ?Team
    {
        $teamOneWins = 0;
        $teamTwoWins = 0;

        $rounds = min($teamOne->getRobots()->count(), $teamTwo->getRobots()->count());

        for ($index = 0; $index < $rounds; $index++) {
            $robotA = $teamOne->getRobots()->get($index);
            $robotB = $teamTwo->getRobots()->get($index);

            if ($robotA === null || $robotB === null) {
                continue;
            }

            if ($robotA->getExperience() > $robotB->getExperience()) {
                $teamOneWins++;
                continue;
            }

            if ($robotB->getExperience() > $robotA->getExperience()) {
                $teamTwoWins++;
            }
        }

        if ($teamOneWins > $teamTwoWins) {
            return $teamOne;
        } elseif ($teamTwoWins > $teamOneWins) {
            return $teamTwo;
        }

        return null;
    }

    private function loadRobot(int $id): Robot
    {
        $robot = $this->robotRepository->findOneBy($id);

        if ($robot === null) {
            throw new RobotServiceException("Robot ID $id does not exist.");
        }

        return $robot;
    }

    public function replayRobotBattle(BattleReplayInstruction $instruction): void
    {
        $this->guardReplacementLimit($instruction->teamOneReplacements());
        $this->guardReplacementLimit($instruction->teamTwoReplacements());

        $battle = $this->resolveBattle(battleId: $instruction->battleId());
        $latestDanceOff = $this->robotDanceOffRepository->findLatestByBattle($battle);

        if ($latestDanceOff === null) {
            throw new RobotServiceException(sprintf(
                'Robot Battle ID %d does not have an initial dance-off to replay.',
                $instruction->battleId()
            ));
        }

        $teamOne = $this->cloneTeamForReplay($latestDanceOff->getTeamOne(), $instruction->teamOneReplacements());
        $teamTwo = $this->cloneTeamForReplay($latestDanceOff->getTeamTwo(), $instruction->teamTwoReplacements());

        $this->teamRepository->save($teamOne);
        $this->teamRepository->save($teamTwo);

        $danceOff = new RobotDanceOff();
        $battle->addOccurrence($danceOff);
        $danceOff->setTeamOne($teamOne);
        $danceOff->setTeamTwo($teamTwo);
        $teamOne->setDanceOff($danceOff);
        $teamTwo->setDanceOff($danceOff);

        $winningTeam = $this->calculateWinningTeam($teamOne, $teamTwo);
        $danceOff->setWinningTeam($winningTeam);

        $this->robotDanceOffRepository->save($danceOff);
    }

    /**
     * @param list<RobotReplacement> $replacements
     */
    private function guardReplacementLimit(array $replacements): void
    {
        if (count($replacements) > 2) {
            throw new RobotServiceException('A maximum of two robot replacements may be submitted per team.');
        }
    }

    /**
     * @param list<RobotReplacement> $replacements
     */
    private function cloneTeamForReplay(?Team $baseline, array $replacements): Team
    {
        if ($baseline === null) {
            throw new RobotServiceException('Baseline team could not be determined for replay.');
        }

        $team = new Team($baseline->getName());

        foreach ($baseline->getRobots() as $robot) {
            $team->addRobot($robot);
        }

        foreach ($replacements as $replacement) {
            $this->applyReplacement($team, $replacement);
        }

        return $team;
    }

    private function applyReplacement(Team $team, RobotReplacement $replacement): void
    {
        $removed = $this->removeRobotFromTeam($team, $replacement->outRobotId());

        if ($removed === false) {
            throw new RobotServiceException(sprintf(
                'Robot ID %d is not part of the current roster and cannot be replaced.',
                $replacement->outRobotId()
            ));
        }

        $incomingRobot = $this->loadRobot($replacement->inRobotId());

        $alreadyPresent = $team->getRobots()->exists(static function (int $_, Robot $robot) use ($incomingRobot): bool {
            return $robot->getId() === $incomingRobot->getId();
        });

        if ($alreadyPresent) {
            throw new RobotServiceException(sprintf(
                'Robot ID %d is already on this team.',
                $incomingRobot->getId()
            ));
        }

        $team->addRobot($incomingRobot);
    }

    private function removeRobotFromTeam(Team $team, int $robotId): bool
    {
        foreach ($team->getRobots() as $robot) {
            if ($robot->getId() === $robotId) {
                $team->removeRobot($robot);

                return true;
            }
        }

        return false;
    }

    private function resolveBattle(?int $battleId): RobotBattle
    {
        if ($battleId === null) {
            $battle = new RobotBattle();
            $this->robotBattleRepository->save($battle);

            return $battle;
        }

        $battle = $this->robotBattleRepository->findOneBy($battleId);

        if ($battle === null) {
            throw new RobotServiceException("Robot Battle ID $battleId does not exist.");
        }

        return $battle;
    }
}
