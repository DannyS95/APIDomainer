<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'robot_dance_off_participants')]
class RobotDanceOffParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Robot::class)]
    #[ORM\JoinColumn(name: 'robot_id', referencedColumnName: 'id')]
    private ?Robot $robot = null;

    #[ORM\ManyToOne(targetEntity: RobotDanceOff::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(name: 'dance_off_id', referencedColumnName: 'id')]
    private ?RobotDanceOff $danceOff = null;

    #[ORM\Column(length: 10)]
    private ?string $team = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRobot(): ?Robot
    {
        return $this->robot;
    }

    public function setRobot(?Robot $robot): self
    {
        $this->robot = $robot;
        return $this;
    }

    public function getDanceOff(): ?RobotDanceOff
    {
        return $this->danceOff;
    }

    public function setDanceOff(?RobotDanceOff $danceOff): self
    {
        $this->danceOff = $danceOff;
        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(string $team): self
    {
        $this->team = $team;
        return $this;
    }
}

