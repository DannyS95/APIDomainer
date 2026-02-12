<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Team;

interface TeamRepositoryInterface
{
    public function findOneById(int $id): ?Team;

    public function save(Team $team): void;

    public function delete(Team $team): void;
}
