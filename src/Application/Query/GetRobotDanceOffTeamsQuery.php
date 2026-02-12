<?php

namespace App\Application\Query;

use Symfony\Component\Validator\Constraints as Assert;

final class GetRobotDanceOffTeamsQuery
{
    public function __construct(
        #[Assert\Positive]
        private readonly int $battleId
    ) {
    }

    public function battleId(): int
    {
        return $this->battleId;
    }
}
