<?php

namespace App\Repository;

use App\Entity\Gym;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gym|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gym|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gym[]    findAll()
 * @method Gym[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GymRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Gym::class);
  }

  public function remove(Gym $entity, bool $flush = true): void
  {
    // Remove all events from gym
    foreach ($entity->getEvents() as $event) {
      $this->_em->remove($event);
    }
    // Remove all routes from gym
    foreach ($entity->getRoutes() as $route) {
      $this->_em->remove($route);
    }
    $this->_em->remove($entity);
    if ($flush) {
      $this->_em->flush();
    }
  }

  public function search($value)
  {
    $qb = $this->createQueryBuilder('Gym')
      ->where('Gym.name LIKE :query')
      ->where('Gym.size LIKE :query')
      ->where('Gym.pc LIKE :query')
      ->where('Gym.address LIKE :query')
      ->where('Gym.city LIKE :query')
      ->setParameter('query', $value);

    $query = $qb->getQuery();

    return $query->execute();
  }

  // /**
  //  * @return Gym[] Returns an array of Gym objects
  //  */
  /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

  /*
    public function findOneBySomeField($value): ?Gym
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}