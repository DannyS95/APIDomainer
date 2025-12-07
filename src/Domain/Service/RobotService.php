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

        $this->createDanceOff($battle, $teamOne, $teamTwo);
    }

    /**
     * Determines the winning team from precomputed power totals.
     */
    private function calculateWinningTeam(Team $teamOne, Team $teamTwo, int $teamOnePower, int $teamTwoPower): ?Team
    {
        if ($teamOnePower > $teamTwoPower) {
            return $teamOne;
        }

        if ($teamTwoPower > $teamOnePower) {
            return $teamTwo;
        }

        return null;
    }

    private function calculateTeamPower(Team $team): int
    {
        $power = 0;

        foreach ($team->getRobots() as $robot) {
            $power += $robot->getExperience();
        }

        return $power;
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

        $this->createDanceOff($battle, $teamOne, $teamTwo);
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
    private function cloneTeamForReplay(?Team $originalTeam, array $replacements): Team
    {
        if ($originalTeam === null) {
            throw new RobotServiceException('Original team could not be determined for replay.');
        }

        $team = new Team($originalTeam->getName());

        foreach ($originalTeam->getRobots() as $robot) {
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

    private function createDanceOff(RobotBattle $battle, Team $teamOne, Team $teamTwo): void
    {
        $danceOff = new RobotDanceOff();
        $battle->addDanceOff($danceOff);
        $danceOff->setTeamOne($teamOne);
        $danceOff->setTeamTwo($teamTwo);
        $teamOne->setDanceOff($danceOff);
        $teamTwo->setDanceOff($danceOff);

        $teamOnePower = $this->calculateTeamPower($teamOne);
        $teamTwoPower = $this->calculateTeamPower($teamTwo);
        $danceOff->setTeamOnePower($teamOnePower);
        $danceOff->setTeamTwoPower($teamTwoPower);

        $winningTeam = $this->calculateWinningTeam($teamOne, $teamTwo, $teamOnePower, $teamTwoPower);
        $danceOff->setWinningTeam($winningTeam);

        $this->robotDanceOffRepository->save($danceOff);
    }
}
