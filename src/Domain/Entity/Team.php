<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'battle_teams')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(name: 'code_name', length: 150)]
    private string $codeName;

    #[ORM\Column(name: 'composition_signature', length: 255)]
    private string $compositionSignature;

    #[ORM\Column(name: 'robot_order', type: 'json')]
    private array $robotOrder = [];

    #[ORM\ManyToMany(targetEntity: Robot::class)]
    #[ORM\JoinTable(name: 'battle_team_robots')]
    private Collection $robots;

    public function __construct(string $name, string $codeName, string $compositionSignature, array $robotOrder = [])
    {
        $this->name = $name;
        $this->codeName = $codeName;
        $this->compositionSignature = $compositionSignature;
        $this->robotOrder = $robotOrder;
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

    public function getCodeName(): string
    {
        return $this->codeName;
    }

    public function setCodeName(string $codeName): void
    {
        $this->codeName = $codeName;
    }

    public function getCompositionSignature(): string
    {
        return $this->compositionSignature;
    }

    public function setCompositionSignature(string $compositionSignature): void
    {
        $this->compositionSignature = $compositionSignature;
    }

    /**
     * @return array<int>
     */
    public function getRobotOrder(): array
    {
        return $this->robotOrder;
    }

    /**
     * @param array<int> $robotOrder
     */
    public function setRobotOrder(array $robotOrder): void
    {
        $this->robotOrder = $robotOrder;
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
}
