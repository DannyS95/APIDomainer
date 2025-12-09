<?php

namespace App\Application\Query;

use Symfony\Component\Validator\Constraints as Assert;

final class GetRobotQuery
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Positive]
        private readonly int $robotId
    )
    {
    }

    public function getRobotId(): int
    {
        return $this->robotId;
    }
}
