<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MapElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MapElement>
 *
 * @method MapElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapElement[]    findAll()
 * @method MapElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapElement::class);
    }

    public function getCrimes(): array
    {
        $qb = $this->createQueryBuilder('m')
                   ->select(
                       'm.type',
                       'm.id',
                       'm.latitude',
                       'm.longitude',
                       'm.markerColor',
                       'm.markerIcon',
                       'm.disclosure'
                   )
                   ->getQuery()
                   ->getArrayResult();

        $result = [];

        foreach ($qb as $item) {
            $result[$item['type']][] = $item;
        }

        return $result;
    }

//    /**
//     * @return MapElement[] Returns an array of MapElement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MapElement
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
