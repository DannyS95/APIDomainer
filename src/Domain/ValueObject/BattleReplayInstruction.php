<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\RobotReplacement;

/**
 * Immutable replay instruction describing roster changes for each team.
 *
 * @psalm-type ReplacementList = list<RobotReplacement>
 */
final class BattleReplayInstruction
{
    private int $battleId;

    /** @var ReplacementList */
    private array $teamOneReplacements;

    /** @var ReplacementList */
    private array $teamTwoReplacements;

    /**
     * @param ReplacementList $teamOneReplacements
     * @param ReplacementList $teamTwoReplacements
     */
    public function __construct(int $battleId, array $teamOneReplacements, array $teamTwoReplacements)
    {
        if ($battleId <= 0) {
            throw new \InvalidArgumentException('Battle identifier must be a positive integer.');
        }

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
}
