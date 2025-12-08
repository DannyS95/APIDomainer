<?php

namespace App\Application\Query;

final class GetRobotDanceOffScoreboardQuery
{
    public function __construct(
        private readonly ?int $year = null,
        private readonly ?int $quarter = null,
        private readonly ?int $page = null,
        private readonly ?int $perPage = null
    ) {
    }

    public function year(): ?int
    {
        return $this->year;
    }

    public function quarter(): ?int
    {
        return $this->quarter;
    }

    public function page(): ?int
    {
        return $this->page;
    }

    public function perPage(): ?int
    {
        return $this->perPage;
    }
}
