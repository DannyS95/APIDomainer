<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Robot;
use Doctrine\ORM\QueryBuilder;

final class RobotQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ENTITY = Robot::class;
    private const ALIAS = 'r';

    protected function buildBaseQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select(self::ALIAS)
            ->from(self::ENTITY, self::ALIAS);
    }

    protected function alias(): string
    {
        return self::ALIAS;
    }

    public function whereId(int $id): self
    {
        $qb = $this->getQueryBuilder();

        $qb->andWhere(sprintf('%s.id = :id', $this->alias()))
           ->setParameter('id', $id);

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
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
}
