<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Franchise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Franchise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Franchise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Franchise[]    findAll()
 * @method Franchise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FranchiseRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Franchise::class);
  }

  /**
   * @throws ORMException
   * @throws OptimisticLockException
   */
  public function remove(Franchise $entity, bool $flush = true): void
  {
    // Get user by id
    $user = $this->getEntityManager()->getReference(
      User::class,
      $entity->getAdmin()
    );
    $user->setFranchise(null);

    // Remove all gyms from franchise
    foreach ($entity->getGyms() as $gym) {
      // Remove all routes from gym
      foreach ($gym->getRoutes() as $route) {
        $this->_em->remove($route);
      }
      // Remove all event
      foreach ($gym->getEvents() as $event) {
        $this->_em->remove($event);
      }
      $this->_em->remove($gym);
    }
    $this->_em->remove($entity);
    if ($flush) {
      $this->_em->flush();
    }
  }

  public function search($value)
  {
    $qb = $this->createQueryBuilder('franchise')
      ->where('franchise.name LIKE :query')
      ->setParameter('query', $value);

    $query = $qb->getQuery();

    return $query->execute();
  }

  // /**
  //  * @return Franchise[] Returns an array of Franchise objects
  //  */
  /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

  /*
    public function findOneBySomeField($value): ?Franchise
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}