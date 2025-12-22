<?php

declare(strict_types=1);

namespace Tests\Stub;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

final class FakeManagerRegistry implements ManagerRegistry
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getManagerForClass(string $class): ?EntityManagerInterface
    {
        return $this->entityManager;
    }
}
