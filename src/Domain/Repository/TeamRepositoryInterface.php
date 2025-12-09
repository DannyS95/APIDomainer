<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Team;
use App\Domain\ValueObject\FilterCriteria;

interface TeamRepositoryInterface
{
    public function findByCriteria(FilterCriteria $filterCriteria): array;

    public function findOneById(int $id): ?Team;

    public function save(Team $team): void;

    public function delete(Team $team): void;
}
