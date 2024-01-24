<?php

namespace App\Domain\Service;

use RobotServiceException;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Infrastructure\DTO\ApiFiltersDTO;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Infrastructure\Request\RobotDanceOffRequest;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;

final class RobotService
{
    public function __construct(private RobotDanceOffRepositoryInterface $robotDanceOffRepository, private RobotRepositoryInterface $robotRepository)
    {
    }

    /**
     * Find all Robot Resources agains't given API Filters.
     *
     * @param ApiFiltersDTO $filters
     * @return array|null
     */
    public function getRobots(ApiFiltersDTO $apiFiltersDTO)
    {
        return $this->robotRepository->findAll($apiFiltersDTO);
    }

    /**
     * Find all Robot Dance Offs.
     *
     * @param ApiFiltersDTO $filters
     * @return array|null
     */
    public function getDanceOffs(ApiFiltersDTO $apiFiltersDTO)
    {
        return $this->robotRepository->findAll($apiFiltersDTO);
    }

    /**
     * Find a robot.
     *
     * @param int $id
     * @return Robot|\Exception
     */
    public function getRobot(int $id): Robot
    {
        $entity = $this->robotRepository->findOneBy($id);

        if ($entity === null) {
            throw new RobotServiceException("There are no robots with id: of $id", 404);
        }

        return $entity;
    }

    /**
     * Set a dance off between two teams of robots by the best match by experience for each time.
     *
     * @param RobotDanceOffRequest $filters
     * @return void
     */
    public function setRobotDanceOff(RobotDanceOffRequest $robotDanceOffRequest): void
    {
        $robotDanceOffRequest->teamA = array_reverse($robotDanceOffRequest->teamA);
        $robotDanceOffRequest->teamB = array_reverse($robotDanceOffRequest->teamB);

        $entities = [];

        foreach ($robotDanceOffRequest->teamA as $key => $robot) {
            if (\in_array($robot, $robotDanceOffRequest->teamB, true)) {
                throw new RobotServiceException("Robot $robot was selected for both teams. Please choose another.", 403);
            }
            $entity = new RobotDanceOff();
            $entity->setRobotOne($this->getRobot($robot));
            $entity->setRobotTwo($this->getRobot($robotDanceOffRequest->teamB[$key]));
            $entity->setWinner(
                $entity->getRobotOne()->getExperience() > $entity->getRobotTwo()->getExperience() ? $entity->getRobotOne() : $entity->getRobotTwo()
            );
            \array_push($entities, $entity);
        }

        $this->robotDanceOffRepository->bulkSave($entities);
    }
}
