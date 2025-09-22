<?php

namespace App\Application\Query;

final class GetRobotQuery
{
    public function __construct(private readonly int $robotId)
    {
    }

    public function getRobotId(): int
    {
        return $this->robotId;
    }
}
