<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'teams')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: Robot::class)]
    #[ORM\JoinTable(name: 'team_robots')]
    private Collection $robots;

    #[ORM\ManyToOne(targetEntity: RobotDanceOff::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(name: 'dance_off_id', referencedColumnName: 'id', nullable: true)]
    private ?RobotDanceOff $danceOff = null;


    public function __construct(string $name)
    {
        $this->name = $name;
        $this->robots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRobots(): Collection
    {
        return $this->robots;
    }

    public function addRobot(Robot $robot): self
    {
        if (!$this->robots->contains($robot)) {
            $this->robots->add($robot);
        }
        return $this;
    }

    public function removeRobot(Robot $robot): self
    {
        if ($this->robots->contains($robot)) {
            $this->robots->removeElement($robot);
        }
        return $this;
    }

    public function setDanceOff(RobotDanceOff $danceOff): self
    {
        $this->danceOff = $danceOff;
        return $this;
    }

    public function getDanceOff(): ?RobotDanceOff
    {
        return $this->danceOff;
    }
}
