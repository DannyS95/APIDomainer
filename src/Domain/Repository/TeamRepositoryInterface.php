<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Team;
use App\Domain\ValueObject\FilterCriteria;

interface TeamRepositoryInterface
{
    public function findAll(FilterCriteria $filterCriteria): array;

    public function findOneBy(int $id): ?Team;

    public function save(Team $team): void;

    public function delete(Team $team): void;
}
