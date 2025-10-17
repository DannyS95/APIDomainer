<?php

namespace App\Domain\ValueObject;

final class RobotReplacement
{
    private int $outRobotId;
    private int $inRobotId;

    public function __construct(int $outRobotId, int $inRobotId)
    {
        if ($outRobotId <= 0 || $inRobotId <= 0) {
            throw new \InvalidArgumentException('Robot identifiers must be positive integers.');
        }

        $this->outRobotId = $outRobotId;
        $this->inRobotId = $inRobotId;
    }

    public function outRobotId(): int
    {
        return $this->outRobotId;
    }

    public function inRobotId(): int
    {
        return $this->inRobotId;
    }
}
