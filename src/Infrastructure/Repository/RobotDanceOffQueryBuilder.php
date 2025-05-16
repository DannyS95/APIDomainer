<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\RobotDanceOff;

final class RobotDanceOffQueryBuilder
{
    private QueryBuilder $qb;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->qb = $entityManager->createQueryBuilder()
            ->select('rdo', 'p', 'r')
            ->from(RobotDanceOff::class, 'rdo')
            ->leftJoin('rdo.participants', 'p')
            ->leftJoin('p.robot', 'r')
            ->addSelect('p')
            ->addSelect('r');
    }

    public function whereClauses(array $filters, array $operations): self
    {
        foreach ($filters as $filter => $value) {
            $operator = $operations[$filter] ?? '=';
            $this->qb->andWhere("rdo.$filter $operator :$filter")
                     ->setParameter($filter, $value);
        }
        return $this;
    }

    public function addSorts(array $sorts): self
    {
        foreach ($sorts as $field => $order) {
            $this->qb->addOrderBy("rdo.$field", $order);
        }
        return $this;
    }

    public function paginate(int $page, int $itemsPerPage): self
    {
        $this->qb->setFirstResult(($page - 1) * $itemsPerPage)
                 ->setMaxResults($itemsPerPage);

        return $this;
    }

    public function fetchArray(): array
    {
        return $this->qb->getQuery()->getResult();
    }
}
