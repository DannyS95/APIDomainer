<?php

declare(strict_types=1);

namespace Tests\Stub;

use App\Infrastructure\Doctrine\Repository\DoctrineRepositoryInterface;
final class FakeDoctrineRepository implements DoctrineRepositoryInterface
{
    public function persist(object $entity): void
    {
    }

    public function save(): void
    {
    }

    public function remove(object $entity): void
    {
    }
}
