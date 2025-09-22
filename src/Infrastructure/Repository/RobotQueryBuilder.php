<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Robot;
use App\Infrastructure\Repository\DoctrineComparisonEnum;

final class RobotQueryBuilder extends AbstractDoctrineQueryBuilder
{
    use DoctrineComparisonFilterTrait;

    private const ENTITY = Robot::class;
    private const ALIAS = 'r';

    private ?QueryBuilder $qb = null;

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function create(): self
    {
        return new self($this->entityManager);
    }

    private function newQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(self::ENTITY, self::ALIAS);
    }

    private function resetQueryBuilder(): QueryBuilder
    {
        $this->qb = $this->newQueryBuilder();

        return $this->qb;
    }

    private function getInitializedQueryBuilder(): QueryBuilder
    {
        if ($this->qb === null) {
            throw new \LogicException('Query builder must be initialised before building the query.');
        }

        return $this->qb;
    }

    /**
     * Add a WHERE clause to filter by ID.
     */
    public function whereId(int $id): self
    {
        $qb = $this->resetQueryBuilder();

        $qb->andWhere(self::ALIAS . '.id = :id')
           ->setParameter('id', $id);

        return $this;
    }

    public function fetchArray(): array
    {
        return $this->fetchArrayResult();
    }

    public function fetchOne(): ?Robot
    {
        $result = $this->fetchOneResult();

        if ($result !== null && !$result instanceof Robot) {
            throw new \LogicException(sprintf('Expected instance of %s, got %s', Robot::class, get_debug_type($result)));
        }

        return $result;
    }

    /**
     * Add sorting to the query.
     */
    public function addSorts(array $sorts): self
    {
        $qb = $this->getInitializedQueryBuilder();

        foreach ($sorts as $field => $order) {
            $qb->addOrderBy(self::ALIAS . ".$field", $order);
        }
        return $this;
    }

    /**
     * Add pagination to the query.
     */
    public function paginate(int $page, int $itemsPerPage): self
    {
        $qb = $this->getInitializedQueryBuilder();

        $qb->setFirstResult(($page - 1) * $itemsPerPage)
           ->setMaxResults($itemsPerPage);
        return $this;
    }

    /**
     * Execute the query and return the result as an array.
     */
    public function fetchArray(): array
    {
        $qb = $this->getInitializedQueryBuilder();
        $results = $qb->getQuery()->getArrayResult();
        $this->qb = null;

        return $results;
    }

    protected function alias(): string
    {
        $qb = $this->getInitializedQueryBuilder();
        $result = $qb->getQuery()->getOneOrNullResult();
        $this->qb = null;

        return $result;
    }
}
