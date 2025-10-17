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
    private Collection $occurrences;

    public function __construct(?DateTimeImmutable $createdAt = null)
    {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->occurrences = new ArrayCollection();
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
    public function getOccurrences(): Collection
    {
        return $this->occurrences;
    }

    public function addOccurrence(RobotDanceOff $occurrence): self
    {
        if (!$this->occurrences->contains($occurrence)) {
            $this->occurrences->add($occurrence);
            $occurrence->setBattle($this);
        }

        return $this;
    }

    public function removeOccurrence(RobotDanceOff $occurrence): self
    {
        if ($this->occurrences->removeElement($occurrence)) {
            if ($occurrence->getBattle() === $this) {
                $occurrence->setBattle(null);
            }
        }

        return $this;
    }
}
