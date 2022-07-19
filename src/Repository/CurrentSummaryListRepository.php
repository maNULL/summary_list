<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CurrentSummaryList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrentSummaryList>
 *
 * @method CurrentSummaryList|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrentSummaryList|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrentSummaryList[]    findAll()
 * @method CurrentSummaryList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrentSummaryListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrentSummaryList::class);
    }

    public function add(CurrentSummaryList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CurrentSummaryList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CurrentSummaryList[] Returns an array of CurrentSummaryList objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CurrentSummaryList
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
