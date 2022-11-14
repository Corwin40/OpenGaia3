<?php

namespace App\Repository\GestApp;

use App\Entity\GestApp\EventGal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventGal|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventGal|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventGal[]    findAll()
 * @method EventGal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventGalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventGal::class);
    }

    public function EventGalPublish($eventName)
    {
        return $this->createQueryBuilder('e')
            ->join('e.event', 'ev')
            ->andWhere('ev.name= :name')
            ->setParameter('name', $eventName)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return EventGal[] Returns an array of EventGal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventGal
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
