<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'robot_battles')]
class RobotBattle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /** @var Collection<int, RobotDanceOff> */
    #[ORM\OneToMany(mappedBy: 'battle', targetEntity: RobotDanceOff::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $danceOffs;

    public function __construct(?DateTimeImmutable $createdAt = null)
    {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->danceOffs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, RobotDanceOff>
     */
    public function getDanceOffs(): Collection
    {
        return $this->danceOffs;
    }

    public function addDanceOff(RobotDanceOff $danceOff): self
    {
        if (!$this->danceOffs->contains($danceOff)) {
            $this->danceOffs->add($danceOff);
            $danceOff->setBattle($this);
        }

        return $this;
    }

    public function removeDanceOff(RobotDanceOff $danceOff): self
    {
        if ($this->danceOffs->removeElement($danceOff)) {
            if ($danceOff->getBattle() === $this) {
                $danceOff->setBattle(null);
            }
        }

        return $this;
    }
}
