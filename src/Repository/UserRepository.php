<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements
  PasswordUpgraderInterface
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, User::class);
  }

  /**
   * Used to upgrade (rehash) the user's password automatically over time.
   */
  public function upgradePassword(
    PasswordAuthenticatedUserInterface $user,
    string $newHashedPassword
  ): void {
    if (!$user instanceof User) {
      throw new UnsupportedUserException(
        sprintf('Instances of "%s" are not supported.', \get_class($user))
      );
    }

    $user->setPassword($newHashedPassword);
    $this->_em->persist($user);
    $this->_em->flush();
  }

  public function search($value)
  {
    $qb = $this->createQueryBuilder('u')
      ->where('u.email LIKE :query')
      ->orWhere('u.firstname LIKE :query')
      ->orWhere('u.lastname LIKE :query')
      ->orWhere('u.username LIKE :query')
      ->setParameter('query', $value)
      ->orderBy('u.firstname', 'ASC');

    $query = $qb->getQuery();

    return $query->execute();
  }

  public function findAllUserMatchingName(string $username): array
  {
    // automatically knows to select Products
    // the "p" is an alias you'll use in the rest of the query
    $qb = $this->createQueryBuilder('u')
      ->where('lower(u.username) like :name')
      ->setParameter('name', '%' . strtolower($username) . '%')
      ->orderBy('u.username', 'ASC');

    $query = $qb->getQuery();

    return $query->execute();
  }
}