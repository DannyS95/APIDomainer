<?php

namespace App\Infrastructure\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Base class for Doctrine-backed write repositories.
 */
abstract class DoctrineRepository extends ServiceEntityRepository
{
    protected EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->entityClass());
        $this->entityManager = $this->getEntityManager();
    }

    /**
     * Concrete repositories must declare the managed entity class.
     */
    abstract protected function entityClass(): string;

    /**
     * Generic delete for repositories; subclasses can rely on contravariance for narrower types.
     */
    public function delete(object $entity): void
    {
        $this->removeEntity($entity);
    }

    protected function persistEntity(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    protected function removeEntity(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @param iterable<object> $entities
     */
    protected function persistEntities(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    protected function findOneEntityById(int $id): ?object
    {
        $repository = $this->entityManager->getRepository($this->entityClass());

        if (!method_exists($repository, 'find')) {
            throw new \LogicException(sprintf(
                'Repository for %s does not support find().',
                $this->entityClass()
            ));
        }

        /** @var object|null $entity */
        $entity = $repository->find($id);

        return $entity;
    }
}
