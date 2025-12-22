<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $entityManager = $registry->getManagerForClass($entityClass);

        if ($entityManager === null) {
            throw new \RuntimeException(sprintf('No entity manager available for %s.', $entityClass));
        }

        $this->entityManager = $entityManager;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
