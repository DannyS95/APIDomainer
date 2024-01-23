<?php

namespace App\Infrastructure\Repository;

use App\Entity\Robot;
use App\Infrastructure\DoctrineComparisonEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

abstract class DoctrineRepository
{
    private ServiceEntityRepositoryInterface $serviceRepo;

    public function __construct(ManagerRegistry $registry)
    {
        $this->serviceRepo = new ServiceEntityRepository(registry: $registry, entityClass: Robot::class);
    }

    public function create(?int $page, ?int $itemsPerPage, ?array $filters, ?array $operations)
    {
        dd(DoctrineComparisonEnum::from($operations['id']));
    }
}
