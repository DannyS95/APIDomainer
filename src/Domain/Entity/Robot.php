<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\RobotDanceOff;
use Doctrine\Common\Collections\Collection;
use App\Infrastructure\Repository\RobotRepository;

#[ORM\Entity(repositoryClass: RobotRepository::class)]
#[ORM\Table(name: 'robots')]
#[ORM\HasLifecycleCallbacks]
class Robot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $powermove = null;

    #[ORM\Column]
    private ?int $experience = null;

    #[ORM\Column]
    private ?bool $outOfOrder = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $avatar = null;

    #[ORM\OneToMany(targetEntity: RobotDanceOff::class, mappedBy: 'robotOne')]
    private Collection $asRobotOne;

    #[ORM\OneToMany(targetEntity: RobotDanceOff::class, mappedBy: 'robotTwo')]
    private Collection $asRobotTwo;

    #[ORM\OneToMany(targetEntity: RobotDanceOff::class, mappedBy: 'winner')]
    private Collection $asWinner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPowermove(): ?string
    {
        return $this->powermove;
    }

    public function setPowermove(?string $powermove): self
    {
        $this->powermove = $powermove;

        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(?int $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function isOutOfOrder(): ?bool
    {
        return $this->outOfOrder;
    }

    public function setOutOfOrder(?bool $outOfOrder): self
    {
        $this->outOfOrder = $outOfOrder;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|RobotDanceOff[]
     */
    public function getAsRobotOne(): Collection
    {
        return $this->asRobotOne;
    }

    /**
     * @return Collection|RobotDanceOff[]
     */
    public function getAsRobotTwo(): Collection
    {
        return $this->asRobotTwo;
    }

    /**
     * @return Collection|RobotDanceOff[]
     */
    public function getAsWinner(): Collection
    {
        return $this->asWinner;
    }
}
