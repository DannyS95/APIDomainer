<?php

namespace App\Domain\Service;

use RobotServiceException;
use App\Domain\Repository\RobotRepositoryInterface;

class RobotValidatorService
{
    private RobotRepositoryInterface $robotRepository;

    public function __construct(RobotRepositoryInterface $robotRepository)
    {
        $this->robotRepository = $robotRepository;
    }

    public function validateRobotIds(array $ids): void
    {
        foreach ($ids as $id) {
            if (!$this->robotRepository->findOneBy(intval($id))) {
                throw new RobotServiceException("Robot ID $id does not exist.");
            }
        }
    }
}
