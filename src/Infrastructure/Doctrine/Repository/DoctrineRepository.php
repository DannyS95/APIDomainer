<?php

namespace App\Infrastructure\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
final class DoctrineRepository implements DoctrineRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function persist(object $entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function remove(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
