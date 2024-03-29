<?php

namespace App\Repository;

use App\Entity\Payments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payments[]    findAll()
 * @method Payments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentsRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Payments::class);
  }

  /**
   * @throws ORMException
   * @throws OptimisticLockException
   */
  public function add(Payments $entity, bool $flush = true): void
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
  public function remove(Payments $entity, bool $flush = true): void
  {
    $this->_em->remove($entity);
    if ($flush) {
      $this->_em->flush();
    }
  }

  public function search($value)
  {
    $qb = $this->createQueryBuilder('payment')
      ->where('payment.type LIKE :query')
      ->orWhere('payment.status LIKE :query')
      ->setParameter('query', $value);

    $query = $qb->getQuery();

    return $query->execute();
  }

  public function getNotPaidById($id)
  {
    $qb = $this->createQueryBuilder('payment')
      ->where('payment.franchise = :id')
      ->andWhere('payment.status != :status')
      ->setParameter('id', $id)
      ->setParameter('status', 'success');

    $query = $qb->getQuery();

    return $query->execute();
  }

  // Get all payments with status success and between two dates
  public function getAllBetweenDates($start, $end)
  {
    $qb = $this->createQueryBuilder('payment')
      ->where('payment.status = :status')
      ->andWhere('payment.updated_at BETWEEN :start AND :end')
      ->setParameter('status', 'success')
      ->setParameter('start', $start)
      ->setParameter('end', $end);

    $query = $qb->getQuery();

    return $query->execute();
  }

  // /**
  //  * @return Payments[] Returns an array of Payments objects
  //  */
  /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

  /*
    public function findOneBySomeField($value): ?Payments
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}