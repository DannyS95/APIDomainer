<?php

namespace App\Domain\Service;

use App\Domain\Repository\RobotRepositoryInterface;

final class RobotValidatorService
{
    public function __construct(private readonly RobotRepositoryInterface $robotRepository)
    {
    }

    /**
     * @param list<int> $ids
     */
    public function validateRobotIds(array $ids): void
    {
        foreach ($ids as $id) {
            if (!$this->robotRepository->findOneById((int) $id)) {
                throw new RobotServiceException("Robot ID $id does not exist.");
            }
        }
    }
}
