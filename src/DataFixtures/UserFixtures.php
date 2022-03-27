<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Event;
use App\Entity\Gym;
use App\Entity\User;
use App\Entity\Franchise;
use App\Entity\Route;
use Doctrine\ORM\EntityManager;

class UserFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < 100; $i++) {
      $user = new User();
      $user->setId($i + 1);
      $user->setEmail($generator->email);
      $user->setFirstname($generator->firstName);
      $user->setLastname($generator->lastName);
      $user->setUsername($generator->userName);
      $user->setPassword($generator->password);
      $user->setBirthdate(
        $generator->dateTimeBetween('-50 years', '-15 years')
      );
      $user->setCreatedAt(new \DateTime());
      $user->setOtp(12345);
      $user->setPicture('default.png');
      $manager->persist($user);
    }
    $user = new User();
    $user->setEmail('krafteck666@gmail.com');
    $user->setFirstname('Arthur');
    $user->setLastname('Vadrot');
    $user->setUsername('Aesahaettr');
    $user->setPassword(
        '$2y$13$YvFuVu1Rtasj5Di.AKcOAuosclgfw/57CFXq4OXRUbx9eXoYPzAei'
    );
    $user->setBirthdate(
        $generator->dateTimeBetween('-24 years', '-23 years')
    );
    $user->setCreatedAt(new \DateTime());
    $user->setOtp(12345);
    $user->setPicture('aesahaettr.gif');
    $manager->persist($user);

    //Admin part
    $adminAccount = new User();
    $adminAccount->setEmail('mcamus@condorcet93.fr');
    $adminAccount->setFirstname('Milan');
    $adminAccount->setLastname('Camus');
    $adminAccount->setUsername('MisterGoodDeal');
    $adminAccount->setPassword(
      '$2y$13$6lBervVYeDGuuwi5VeSv3e.H0YlWo03yNhWOgPWIA8BIHkKIC/InC'
    );
    $adminAccount->setBirthdate(
      $generator->dateTimeBetween('-22 years', '-21 years')
    );
    $adminAccount->setCreatedAt(new \DateTime());
    $adminAccount->setOtp(12345);
    $adminAccount->setIsActivated(true);
    $adminAccount->setPicture('mistergooddeal.jpg');
    $manager->persist($adminAccount);

    $this->generateFranchise($manager);

    $manager->flush();
  }

  // Create Franchise
  public function generateFranchise(EntityManager $em)
  {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < 10; $i++) {
      $franchise = new Franchise();
      $franchise->setAdmin($generator->numberBetween(1, 100));
      $franchise->setName($generator->company);
      $em->persist($franchise);
      $this->generateGym($em, $franchise);
    }
    $em->flush();
  }

  // Create Gyms
  public function generateGym(EntityManager $em, Franchise $franchise)
  {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < $generator->numberBetween(3, 10); $i++) {
      $gym = new Gym();
      $gym->setId($i + 1);
      $gym->setName($generator->company);
      $gym->setAddress($generator->address);
      $gym->setPc($generator->postcode);
      $gym->setCity($generator->city);
      $gym->setSize($generator->numberBetween(250, 10000));
      $gym->setFranchise($franchise);
      $em->persist($gym);
      $this->generateEvents($em, $gym);
      $this->generateRoute($em, $gym);
    }
    $em->flush();
  }

  public function generateRoute(EntityManager $em, Gym $gym) {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < $generator->numberBetween(3, 10); $i++) {
      $route = new Route();
      $route->setId($i + 1);
      $route->setOpened($generator->numberBetween(0, 1));
      $route->setGym($gym);
      $route->setDifficulty($generator->numberBetween(1, 42));
      $em->persist($route);
    }
    $em->flush();
  }

  // Generate events
  public function generateEvents(EntityManager $em, Gym $gym)
  {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < $generator->numberBetween(1, 10); $i++) {
      $event = new Event();
      $event->setId($i + 1);
      $event->setTitle("Event title");
      $event->setDescription(
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque vitae mauris odio. Sed nec enim vitae nisl commodo semper vitae eu risus. Fusce at ligula dictum"
      );
      $event->setEventDate(new \DateTime());
      $event->setEndDate($generator->dateTimeBetween('+1 hours', '+6 hours'));
      $event->setGym($gym);
      $em->persist($event);
    }
    $em->flush();
  }
}