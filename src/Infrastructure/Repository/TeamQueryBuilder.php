<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Team;
use App\Infrastructure\Doctrine\QueryBuilder\AbstractDoctrineQueryBuilder;
use Doctrine\ORM\QueryBuilder;

final class TeamQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ENTITY = Team::class;
    private const ALIAS = 't';

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

    /**
     * @return array<int, Team>
     */
    public function fetch(): array
    {
        $results = $this->fetchResult();

        foreach ($results as $result) {
            if (!$result instanceof Team) {
                throw new \LogicException(sprintf('Expected instance of %s, got %s', Team::class, get_debug_type($result)));
            }
        }

        /** @var array<int, Team> $results */
        return $results;
    }
}
