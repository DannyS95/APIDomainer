<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RobotBattle;
use App\Domain\Repository\RobotBattleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class RobotBattleRepository implements RobotBattleRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findOneBy(int $id): ?RobotBattle
    {
        return $this->entityManager->getRepository(RobotBattle::class)->find($id);
    }

    public function save(RobotBattle $battle): void
    {
        $this->entityManager->persist($battle);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(RobotBattle::class)->findAll();
    }
}
