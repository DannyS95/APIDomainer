<?php

namespace App\Infrastructure\Response;

final class RobotResponse
{
    public function __construct(
        private string $id,
        private string $name,
        private string $type,
        private int $health,
        private int $powerLevel,
        private string $status
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function getPowerLevel(): int
    {
        return $this->powerLevel;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
