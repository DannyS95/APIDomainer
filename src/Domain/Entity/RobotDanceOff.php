<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'robot_dance_offs')]
class RobotDanceOff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Team::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'team_one_id', referencedColumnName: 'id', nullable: false)]
    private ?Team $teamOne = null;

    #[ORM\ManyToOne(targetEntity: Team::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'team_two_id', referencedColumnName: 'id', nullable: false)]
    private ?Team $teamTwo = null;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'winner_id', referencedColumnName: 'id', nullable: true)]
    private ?Team $winner = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamOne(): ?Team
    {
        return $this->teamOne;
    }

    public function setTeamOne(Team $teamOne): self
    {
        $this->teamOne = $teamOne;
        return $this;
    }

    public function getTeamTwo(): ?Team
    {
        return $this->teamTwo;
    }

    public function setTeamTwo(Team $teamTwo): self
    {
        $this->teamTwo = $teamTwo;
        return $this;
    }

    public function getWinner(): ?Team
    {
        return $this->winner;
    }

    public function setWinner(?Team $winner): self
    {
        $this->winner = $winner;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}