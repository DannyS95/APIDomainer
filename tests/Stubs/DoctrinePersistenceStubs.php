<?php

declare(strict_types=1);

namespace Doctrine\Persistence;

use Doctrine\ORM\EntityManagerInterface;

interface ManagerRegistry
{
    public function getManagerForClass(string $class): ?EntityManagerInterface;
}
