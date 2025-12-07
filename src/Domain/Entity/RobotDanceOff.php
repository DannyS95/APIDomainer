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
    #[ORM\JoinColumn(name: 'winning_team_id', referencedColumnName: 'id', nullable: true)]
    private ?Team $winningTeam = null;

    #[ORM\ManyToOne(targetEntity: RobotBattle::class, inversedBy: 'danceOffs')]
    #[ORM\JoinColumn(name: 'robot_battle_id', referencedColumnName: 'id', nullable: false)]
    private ?RobotBattle $battle = null;

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

    public function getWinningTeam(): ?Team
    {
        return $this->winningTeam;
    }

    public function setWinningTeam(?Team $winningTeam): self
    {
        $this->winningTeam = $winningTeam;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getBattle(): ?RobotBattle
    {
        return $this->battle;
    }

    public function setBattle(?RobotBattle $battle): self
    {
        $this->battle = $battle;

        return $this;
    }
}
