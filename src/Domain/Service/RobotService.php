<?php

namespace App\Domain\Service;

use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOffHistory;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\Team;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\Repository\RobotDanceOffHistoryRepositoryInterface;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Domain\ValueObject\BattleReplayInstruction;
use App\Domain\ValueObject\DanceOffTeams;
use App\Domain\ValueObject\RobotReplacement;

final class RobotService
{
    public function __construct(
        private readonly RobotRepositoryInterface $robotRepository,
        private readonly RobotDanceOffRepositoryInterface $robotDanceOffRepository,
        private readonly RobotDanceOffHistoryRepositoryInterface $robotDanceOffHistoryRepository,
        private readonly TeamRepositoryInterface $teamRepository,
        private readonly RobotValidatorService $robotValidatorService
    ) {}

    /**
     * Set a dance off between two teams of robots.
     */
    public function setRobotDanceOff(DanceOffTeams $danceOffTeams): void
    {
        $robotIds = $danceOffTeams->allRobotIds();

        $this->robotValidatorService->validateRobotIds($robotIds);

        $battle = $this->createNewBattle();

        // Create the teams and associate robots
        $teamOne = $this->buildTeam($danceOffTeams->teamOneRobotIds());
        $teamTwo = $this->buildTeam($danceOffTeams->teamTwoRobotIds());

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
        $robot = $this->robotRepository->findOneById($id);

        if ($robot === null) {
            throw new RobotServiceException("Robot ID $id does not exist.");
        }

        return $robot;
    }

    public function replayRobotBattle(BattleReplayInstruction $instruction): void
    {
        $originalBattle = $this->resolveBattle($instruction->battleId());
        $latestDanceOff = $this->robotDanceOffRepository->findLatestByBattle($originalBattle);

        if ($latestDanceOff === null) {
            throw new RobotServiceException(sprintf(
                'Robot Battle ID %d does not have an initial dance-off to replay.',
                $instruction->battleId()
            ));
        }

        $teamOne = $latestDanceOff->getTeamOne();
        $teamTwo = $latestDanceOff->getTeamTwo();

        $this->updateTeamForReplay($teamOne, $instruction->teamOneReplacements());
        $this->updateTeamForReplay($teamTwo, $instruction->teamTwoReplacements());

        $this->teamRepository->save($teamOne);
        $this->teamRepository->save($teamTwo);

        $this->createDanceOff($originalBattle, $teamOne, $teamTwo);
    }

    /**
     * @param list<RobotReplacement> $replacements
     */
    private function updateTeamForReplay(?Team $team, array $replacements): void
    {
        if ($team === null) {
            throw new RobotServiceException('Original team could not be determined for replay.');
        }

        $order = $this->resolveRobotOrder($team);

        foreach ($replacements as $replacement) {
            $order = $this->applyReplacement($team, $replacement, $order);
        }

        $team->setRobotOrder($order);
        $team->setCompositionSignature($this->generateCompositionSignature($order));
    }

    /**
     * @param list<int> $order
     */
    private function applyReplacement(Team $team, RobotReplacement $replacement, array $order): array
    {
        $outRobotId = $replacement->outRobotId();
        $inRobotId = $replacement->inRobotId();

        $order = $this->removeRobotFromOrder($order, $outRobotId);

        $incomingRobot = $this->loadRobot($inRobotId);

        if (in_array($incomingRobot->getId(), $order, true)) {
            throw new RobotServiceException(sprintf(
                'Robot ID %d is already on this team.',
                $incomingRobot->getId()
            ));
        }

        if ($this->removeRobotFromTeam($team, $outRobotId) === false) {
            throw new RobotServiceException(sprintf(
                'Robot ID %d is not part of the current roster and cannot be replaced.',
                $outRobotId
            ));
        }
        $team->addRobot($incomingRobot);

        $order[] = $incomingRobot->getId();

        return $order;
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

    private function resolveBattle(int $battleId): RobotDanceOffHistory
    {
        $battle = $this->robotDanceOffHistoryRepository->findOneById($battleId);

        if ($battle === null) {
            throw new RobotServiceException("Robot Battle ID $battleId does not exist.");
        }

        return $battle;
    }

    private function createNewBattle(): RobotDanceOffHistory
    {
        $battle = new RobotDanceOffHistory();
        $this->robotDanceOffHistoryRepository->save($battle);

        return $battle;
    }

    private function createDanceOff(RobotDanceOffHistory $battle, Team $teamOne, Team $teamTwo): void
    {
        $danceOff = new RobotDanceOff();
        $battle->addDanceOff($danceOff);
        $danceOff->setTeamOne($teamOne);
        $danceOff->setTeamTwo($teamTwo);

        $teamOnePower = $this->calculateTeamPower($teamOne);
        $teamTwoPower = $this->calculateTeamPower($teamTwo);
        $danceOff->setTeamOnePower($teamOnePower);
        $danceOff->setTeamTwoPower($teamTwoPower);

        $winningTeam = $this->calculateWinningTeam($teamOne, $teamTwo, $teamOnePower, $teamTwoPower);
        $danceOff->setWinningTeam($winningTeam);

        $this->robotDanceOffRepository->save($danceOff);
    }

    /**
     * @param list<int> $robotIds
     */
    private function buildTeam(array $robotIds): Team
    {
        $signature = $this->generateCompositionSignature($robotIds);
        $name = $this->generateTeamName();
        $team = new Team($name, $name, $signature, $robotIds);

        foreach ($robotIds as $robotId) {
            $team->addRobot($this->loadRobot($robotId));
        }

        return $team;
    }

    /**
     * @param list<int> $robotIds
     */
    private function generateCompositionSignature(array $robotIds): string
    {
        return hash('sha256', json_encode($robotIds, JSON_THROW_ON_ERROR));
    }

    private function generateTeamName(): string
    {
        $adjectives = ['Crimson', 'Electric', 'Atomic', 'Neon', 'Galactic', 'Midnight'];
        $nouns = ['Falcons', 'Golems', 'Circuits', 'Dynamos', 'Rangers', 'Titans'];

        return sprintf(
            '%s %s #%d',
            $adjectives[array_rand($adjectives)],
            $nouns[array_rand($nouns)],
            random_int(100, 999)
        );
    }

    /**
     * @return list<int>
     */
    private function resolveRobotOrder(Team $team): array
    {
        $order = $team->getRobotOrder();

        if ($order !== []) {
            return array_values($order);
        }

        $ids = [];

        foreach ($team->getRobots() as $robot) {
            $ids[] = (int) $robot->getId();
        }

        return $ids;
    }

    /**
     * @param list<int> $order
     * @return list<int>
     */
    private function removeRobotFromOrder(array $order, int $robotId): array
    {
        $filtered = array_values(array_filter(
            $order,
            static fn (int $id): bool => $id !== $robotId
        ));

        if (count($filtered) === count($order)) {
            throw new RobotServiceException(sprintf(
                'Robot ID %d is not part of the current roster and cannot be replaced.',
                $robotId
            ));
        }

        return $filtered;
    }
}
