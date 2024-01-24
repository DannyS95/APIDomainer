<?php

namespace App\Infrastructure\Response;

class RobotDanceOffResponse
{
    private int $id;
    private string $name;
    private string $powermove;
    private int $experience;
    private bool $outOfOrder;
    private string $avatar;

    public function __construct(
        int $id,
        string $name,
        string $powermove,
        int $experience,
        bool $outOfOrder,
        string $avatar
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->powermove = $powermove;
        $this->experience = $experience;
        $this->outOfOrder = $outOfOrder;
        $this->avatar = $avatar;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPowermove(): string
    {
        return $this->powermove;
    }

    public function getExperience(): int
    {
        return $this->experience;
    }

    public function isOutOfOrder(): bool
    {
        return $this->outOfOrder;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }
}