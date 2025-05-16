<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'robot_dance_offs')]
class RobotDanceOff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'danceOff', targetEntity: RobotDanceOffParticipant::class)]
    private Collection $participants;

    #[ORM\ManyToOne(targetEntity: Robot::class)]
    #[ORM\JoinColumn(name: 'winner_id', referencedColumnName: 'id', nullable: true)]
    private ?Robot $winner = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function getWinner(): ?Robot
    {
        return $this->winner;
    }

    public function setWinner(?Robot $winner): self
    {
        $this->winner = $winner;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getTeamOne(): Collection
    {
        return $this->participants->filter(fn($p) => $p->getTeam() === 'teamOne');
    }

    public function getTeamTwo(): Collection
    {
        return $this->participants->filter(fn($p) => $p->getTeam() === 'teamTwo');
    }
}
