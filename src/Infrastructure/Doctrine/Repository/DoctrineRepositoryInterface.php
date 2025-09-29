<?php

namespace App\Infrastructure\Doctrine\Repository;

interface DoctrineRepositoryInterface
{
    public function persist(object $entity): void;

    public function save(): void;

    public function remove(object $entity): void;
}
