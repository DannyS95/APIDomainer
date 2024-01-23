<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RobotRepository;

#[ORM\Entity(repositoryClass: RobotRepository::class)]
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

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
