<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use App\Infrastructure\Repository\RobotDanceOffRepository;

#[ORM\Entity(repositoryClass: RobotDanceOffRepository::class)]
#[ORM\Table(name: 'robot_dance_offs')]
class RobotDanceOff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ManyToOne(targetEntity: Robot::class, inversedBy: 'asRobotOne', fetch: 'LAZY')]
    #[JoinColumn(name: 'robot_one', referencedColumnName: 'id')]
    private Robot $robotOne;

    #[ManyToOne(targetEntity: Robot::class, inversedBy: 'asRobotTwo', fetch: 'LAZY')]
    #[JoinColumn(name: 'robot_two', referencedColumnName: 'id')]
    private Robot $robotTwo;

    #[ManyToOne(targetEntity: Robot::class, inversedBy: 'asWinner', fetch: 'LAZY')]
    #[JoinColumn(name: 'winner', referencedColumnName: 'id')]
    private Robot $winner;

    public function getId(): int
    {
        return $this->id;
    }

    public function getRobotOne(): Robot
    {
        return $this->robotOne;
    }

    public function setRobotOne(Robot $robotOne): static
    {
        $this->robotOne = $robotOne;

        return $this;
    }

    public function getRobotTwo(): Robot
    {
        return $this->robotTwo;
    }

    public function setRobotTwo(?Robot $robotTwo): static
    {
        $this->robotTwo = $robotTwo;

        return $this;
    }

    public function getWinner(): Robot
    {
        return $this->winner;
    }

    public function setWinner(Robot $winner): static
    {
        $this->winner = $winner;

        return $this;
    }
}
