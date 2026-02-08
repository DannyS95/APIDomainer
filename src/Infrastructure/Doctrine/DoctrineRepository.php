<?php

namespace App\Infrastructure\Doctrine;

use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Doctrine\QueryBuilder\AbstractDoctrineQueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Base class for Doctrine-backed repositories using a custom query builder.
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
     * Concrete repositories must supply their query builder.
     */
    abstract protected function queryBuilder(): AbstractDoctrineQueryBuilder;

    /**
     * Concrete repositories must declare the managed entity class.
     */
    abstract protected function entityClass(): string;

    /**
     * Override to define default sorts when none are supplied.
     *
     * @return array<string, string>
     */
    protected function defaultSorts(): array
    {
        return [];
    }

    /**
     * Override to define default page size when none is supplied.
     */
    protected function defaultItemsPerPage(): int
    {
        return 50;
    }

    /**
     * Maximum items per page to guard against abusive queries.
     */
    protected function maxItemsPerPage(): int
    {
        return 100;
    }

    /**
     * Apply criteria and fetch results using the repository's query builder and defaults.
     */
    public function findByCriteria(FilterCriteria $filterCriteria): array
    {
        return $this->fetchWithCriteria(
            $this->queryBuilder()->create(),
            $filterCriteria,
            $this->defaultSorts(),
            $this->defaultItemsPerPage()
        );
    }

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
        return $this->entityManager
            ->getRepository($this->entityClass())
            ->find($id);
    }

    /**
     * Shared helper to apply filter criteria to a Doctrine-backed query builder.
     *
     * @template T of object
     * @param AbstractDoctrineQueryBuilder $queryBuilder
     * @param FilterCriteria $filterCriteria
     * @param array<string, string> $defaultSorts
     * @param int $defaultItemsPerPage
     * @return array<int, T>
     */
    protected function fetchWithCriteria(
        AbstractDoctrineQueryBuilder $queryBuilder,
        FilterCriteria $filterCriteria,
        array $defaultSorts = [],
        int $defaultItemsPerPage = 50
    ): array {
        $page = max(1, $filterCriteria->getPage());
        $itemsPerPage = $filterCriteria->getItemsPerPage() > 0
            ? $filterCriteria->getItemsPerPage()
            : $defaultItemsPerPage;
        $itemsPerPage = min($itemsPerPage, $this->maxItemsPerPage());
        $sorts = $filterCriteria->getSorts();

        if (empty($sorts)) {
            $sorts = $defaultSorts;
        }

        return $queryBuilder
            ->whereClauses(
                $filterCriteria->getFilters(),
                $filterCriteria->getOperations()
            )
            ->addSorts($sorts)
            ->paginate($page, $itemsPerPage)
            ->fetch();
    }
}
