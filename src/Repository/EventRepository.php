<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Event::class);
  }

  /**
   * @throws ORMException
   * @throws OptimisticLockException
   */
  public function add(Event $entity, bool $flush = true): void
  {
    $this->_em->persist($entity);
    if ($flush) {
      $this->_em->flush();
    }
  }

  /**
   * @throws ORMException
   * @throws OptimisticLockException
   */
  public function remove(Event $entity, bool $flush = true): void
  {
    $this->_em->remove($entity);
    if ($flush) {
      $this->_em->flush();
    }
  }

  public function search($value)
  {
    $qb = $this->createQueryBuilder('event')
      ->where('event.title LIKE :query')
      ->orWhere('event.description LIKE :query')
      ->setParameter('query', $value);

    $query = $qb->getQuery();

    return $query->execute();
  }

  public function searchByGym($value, $gym) {
      $qb = $this->createQueryBuilder('event')
          ->where('event.title LIKE :query')
          ->orWhere('event.description LIKE :query')
          ->andWhere('event.gym_id = :gym')
          ->setParameter('query', $value)
          ->setParameter('gym', $gym);

      $query = $qb->getQuery();

      return $query->execute();
  }

  // /**
  //  * @return Event[] Returns an array of Event objects
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
    public function findOneBySomeField($value): ?Event
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