<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\DanceOffs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DanceOffs>
 *
 * @method DanceOffs|null find($id, $lockMode = null, $lockVersion = null)
 * @method DanceOffs|null findOneBy(array $criteria, array $orderBy = null)
 * @method DanceOffs[]    findAll()
 * @method DanceOffs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DanceOffsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DanceOffs::class);
    }

//    /**
//     * @return DanceOffs[] Returns an array of DanceOffs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DanceOffs
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
