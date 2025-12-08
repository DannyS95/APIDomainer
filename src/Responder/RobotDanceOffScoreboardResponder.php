<?php

namespace App\Responder;

use App\Infrastructure\Response\RobotDanceOffScoreboardResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class RobotDanceOffScoreboardResponder
{
    /**
     * @param array<int, RobotDanceOffScoreboardResponse> $items
     */
    public function respond(array $items): Collection
    {
        return new ArrayCollection($items);
    }
}
