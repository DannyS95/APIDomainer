<?php

namespace App\Domain\ValueObject;

/**
 * Immutable replay instruction describing roster changes for each team.
 *
 * @psalm-type ReplacementList = list<RobotReplacement>
 */
final class BattleReplayInstruction
{
    private const MAX_REPLACEMENTS_PER_TEAM = 2;

    private readonly int $battleId;

    /** @var ReplacementList */
    private readonly array $teamOneReplacements;

    /** @var ReplacementList */
    private readonly array $teamTwoReplacements;

    /**
     * @param ReplacementList $teamOneReplacements
     * @param ReplacementList $teamTwoReplacements
     */
    public function __construct(int $battleId, array $teamOneReplacements, array $teamTwoReplacements)
    {
        if ($battleId <= 0) {
            throw new \InvalidArgumentException('Battle identifier must be a positive integer.');
        }

        $this->assertTeamReplacements($teamOneReplacements, 'team one');
        $this->assertTeamReplacements($teamTwoReplacements, 'team two');

        $this->battleId = $battleId;
        $this->teamOneReplacements = array_values($teamOneReplacements);
        $this->teamTwoReplacements = array_values($teamTwoReplacements);
    }

    public function battleId(): int
    {
        return $this->battleId;
    }

    /**
     * @return ReplacementList
     */
    public function teamOneReplacements(): array
    {
        return $this->teamOneReplacements;
    }

    /**
     * @return ReplacementList
     */
    public function teamTwoReplacements(): array
    {
        return $this->teamTwoReplacements;
    }

    /**
     * @param list<RobotReplacement> $replacements
     */
    private function assertTeamReplacements(array $replacements, string $teamLabel): void
    {
        if (count($replacements) > self::MAX_REPLACEMENTS_PER_TEAM) {
            throw new \InvalidArgumentException(sprintf(
                'A maximum of %d robot replacements may be submitted for %s.',
                self::MAX_REPLACEMENTS_PER_TEAM,
                $teamLabel
            ));
        }

        $outRobotIds = [];
        $inRobotIds = [];

        foreach ($replacements as $index => $replacement) {
            if (!$replacement instanceof RobotReplacement) {
                throw new \InvalidArgumentException(sprintf(
                    'Replacement at index %d for %s must be a RobotReplacement instance.',
                    $index,
                    $teamLabel
                ));
            }

            $outRobotId = $replacement->outRobotId();
            $inRobotId = $replacement->inRobotId();

            if ($outRobotId === $inRobotId) {
                throw new \InvalidArgumentException(sprintf(
                    'Replacement at index %d for %s cannot swap a robot with itself.',
                    $index,
                    $teamLabel
                ));
            }

            if (isset($outRobotIds[$outRobotId])) {
                throw new \InvalidArgumentException(sprintf(
                    'Duplicate outgoing robot ID %d found for %s replacements.',
                    $outRobotId,
                    $teamLabel
                ));
            }

            if (isset($inRobotIds[$inRobotId])) {
                throw new \InvalidArgumentException(sprintf(
                    'Duplicate incoming robot ID %d found for %s replacements.',
                    $inRobotId,
                    $teamLabel
                ));
            }

            $outRobotIds[$outRobotId] = true;
            $inRobotIds[$inRobotId] = true;
        }
    }
}
