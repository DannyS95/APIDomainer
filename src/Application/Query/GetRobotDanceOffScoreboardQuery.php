<?php

namespace App\Application\Query;

use Symfony\Component\Validator\Constraints as Assert;

final class GetRobotDanceOffScoreboardQuery
{
    public function __construct(
        #[Assert\Positive]
        private readonly ?int $year = null,
        #[Assert\Range(min: 1, max: 4)]
        private readonly ?int $quarter = null,
        #[Assert\Positive]
        private readonly ?int $page = null,
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(100)]
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
