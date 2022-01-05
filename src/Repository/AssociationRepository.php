<?php

namespace App\Repository;

use App\Entity\Association;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Association|null find($id, $lockMode = null, $lockVersion = null)
 * @method Association|null findOneBy(array $criteria, array $orderBy = null)
 * @method Association[]    findAll()
 * @method Association[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Association::class);
    }


    // /**
    //  * @return Association[] Returns an array of Association objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByDistance($lat, $lng, $distance = 5)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
            ->setParameter('distance', $distance)
            ->andWhere('6353 * 2 * ASIN(SQRT( POWER(SIN((a.lat - :lat) * pi()/180 / 2  ), 2 )
            +COS(a.lat * pi()/180) * COS(:lat * pi()/180 ) * POWER(SIN((a.lng - :lng) * pi()/180 / 2), 2 )))  <=  :distance')
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Association
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
