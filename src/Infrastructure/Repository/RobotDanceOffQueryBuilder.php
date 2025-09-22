<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\RobotDanceOff;
use App\Infrastructure\DoctrineComparisonEnum;

final class RobotDanceOffQueryBuilder
{
    private const ENTITY = RobotDanceOff::class;
    private const ALIAS = 'rdo';
    private const TEAM_ONE_ALIAS = 'teamOne';
    private const TEAM_TWO_ALIAS = 'teamTwo';
    private const WINNER_ALIAS = 'winningTeam';

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
            ->select(self::ALIAS, self::TEAM_ONE_ALIAS, self::TEAM_TWO_ALIAS, self::WINNER_ALIAS)
            ->from(self::ENTITY, self::ALIAS)
            ->leftJoin(self::ALIAS . '.teamOne', self::TEAM_ONE_ALIAS)
            ->leftJoin(self::ALIAS . '.teamTwo', self::TEAM_TWO_ALIAS)
            ->leftJoin(self::ALIAS . '.winningTeam', self::WINNER_ALIAS);
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

    public function whereClauses(array $filters, array $operations): self
    {
        $qb = $this->resetQueryBuilder();

        foreach ($filters as $filter => $value) {
            $operation = $operations[$filter] ?? DoctrineComparisonEnum::eq->value;

            if (!DoctrineComparisonEnum::tryFrom($operation)) {
                throw new \InvalidArgumentException("Invalid operation: $operation");
            }

            $qb->andWhere(self::ALIAS . ".$filter $operation :$filter")
               ->setParameter($filter, $value);
        }
        return $this;
    }

    public function addSorts(array $sorts): self
    {
        $qb = $this->getInitializedQueryBuilder();

        foreach ($sorts as $field => $order) {
            $qb->addOrderBy(self::ALIAS . ".$field", $order);
        }
        return $this;
    }

    public function paginate(int $page, int $itemsPerPage): self
    {
        $qb = $this->getInitializedQueryBuilder();

        $qb->setFirstResult(($page - 1) * $itemsPerPage)
           ->setMaxResults($itemsPerPage);

        return $this;
    }

    public function fetchArray(): array
    {
        $qb = $this->getInitializedQueryBuilder();
        $results = $qb->getQuery()->getResult();
        $this->qb = null;

        return $results;
    }
}
