<?php

namespace App\Domain\ValueObject;

final class DanceOffTeams
{
    /** @var array<int> */
    private array $teamOne;

    /** @var array<int> */
    private array $teamTwo;

    /**
     * @param array<int> $teamOne
     * @param array<int> $teamTwo
     */
    public function __construct(array $teamOne, array $teamTwo)
    {
        $this->teamOne = array_values($teamOne);
        $this->teamTwo = array_values($teamTwo);
    }

    /**
     * @return array<int>
     */
    public function teamOneRobotIds(): array
    {
        return $this->teamOne;
    }

    /**
     * @return array<int>
     */
    public function teamTwoRobotIds(): array
    {
        return $this->teamTwo;
    }

    /**
     * @return array<int>
     */
    public function allRobotIds(): array
    {
        return [...$this->teamOne, ...$this->teamTwo];
    }
}
