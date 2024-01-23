<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\Repository\DanceOffsRepository;

#[ORM\Entity(repositoryClass: DanceOffsRepository::class)]
class DanceOffs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $robotOne = null;

    #[ORM\Column(nullable: true)]
    private ?int $robotTwo = null;

    #[ORM\Column]
    private ?int $winner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRobotOne(): ?int
    {
        return $this->robotOne;
    }

    public function setRobotOne(int $robotOne): static
    {
        $this->robotOne = $robotOne;

        return $this;
    }

    public function getRobotTwo(): ?int
    {
        return $this->robotTwo;
    }

    public function setRobotTwo(?int $robotTwo): static
    {
        $this->robotTwo = $robotTwo;

        return $this;
    }

    public function getWinner(): ?int
    {
        return $this->winner;
    }

    public function setWinner(int $winner): static
    {
        $this->winner = $winner;

        return $this;
    }
}
