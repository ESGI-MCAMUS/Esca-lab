<?php

namespace App\Repository;

use App\Entity\Route;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Route|null find($id, $lockMode = null, $lockVersion = null)
 * @method Route|null findOneBy(array $criteria, array $orderBy = null)
 * @method Route[]    findAll()
 * @method Route[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RouteRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Route::class);
  }

  public function remove(Route $entity, bool $flush = true): void
  {
    $this->_em->remove($entity);
    if ($flush) {
      $this->_em->flush();
    }
  }

  public function search($value)
  {
    $qb = $this->createQueryBuilder('route')
      ->where('route.name LIKE :query')
      ->setParameter('query', $value);

    $query = $qb->getQuery();

    return $query->execute();
  }

  // /**
  //  * @return Route[] Returns an array of Route objects
  //  */
  /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

  /*
    public function findOneBySomeField($value): ?Route
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}