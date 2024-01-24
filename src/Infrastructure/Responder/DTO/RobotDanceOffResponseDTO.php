<?php

namespace App\Infrastructure\Responder\DTO;

class RobotDanceOffResponseDTO
{
    private int $id;
    private string $robotOne;
    private string $robotTwo;
    private int $winner;

    public function __construct(
        int $id,
        int $robotOne,
        int $robotTwo,
        int $winner,
    ) {
        $this->id = $id;
        $this->robotOne = $robotOne;
        $this->robotTwo = $robotTwo;
        $this->winner = $winner;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRobotOne(): int
    {
        return $this->robotOne;
    }

    public function getRobotTwo(): int
    {
        return $this->robotTwo;
    }

    public function getWinner(): int
    {
        return $this->winner;
    }
}
