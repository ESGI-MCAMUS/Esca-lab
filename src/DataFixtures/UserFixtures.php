<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Event;
use App\Entity\Media;
use App\Entity\Gym;
use App\Entity\User;
use App\Entity\Payments;
use App\Entity\Franchise;
use App\Entity\Route;
use Doctrine\ORM\EntityManager;

class UserFixtures extends Fixture {
  public function load(ObjectManager $manager): void {
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
      $this->generateMedias($manager, $user);
    }
    $user = new User();
    $user->setEmail('krafteck666@gmail.com');
    $user->setFirstname('Arthur');
    $user->setLastname('Vadrot');
    $user->setUsername('Aesahaettr');
    $user->setPassword(
      '$2y$13$YvFuVu1Rtasj5Di.AKcOAuosclgfw/57CFXq4OXRUbx9eXoYPzAei'
    );
    $user->setBirthdate($generator->dateTimeBetween('-24 years', '-23 years'));
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
    $adminAccount->setRoles(['ROLE_SUPER_ADMIN']);
    $manager->persist($adminAccount);

    //Admin part
    $otherAccount = new User();
    $otherAccount->setEmail('thomas.geoffron.sio@gmail.com');
    $otherAccount->setFirstname('Thomas');
    $otherAccount->setLastname('GEOFFRON');
    $otherAccount->setUsername('Arkeonn');
    $otherAccount->setPassword(
      '$2y$13$d8tv3hxAQ0kwCAPx.3bra.QuH6v4MUpyMNEcWVlesrSHvajnbidF2'
    );
    $otherAccount->setBirthdate(
      $generator->dateTimeBetween('-22 years', '-21 years')
    );
    $otherAccount->setCreatedAt(new \DateTime());
    $otherAccount->setOtp(12345);
    $otherAccount->setIsActivated(true);
    $otherAccount->setPicture('default.jpg');
    $otherAccount->setRoles(['ROLE_SUPER_ADMIN']);
    $manager->persist($otherAccount);


    $this->generateUser(
      $manager,
      $generator,
      'Admin',
      'Franchise',
      'AdminFranchise',
      'admin@franchise.fr',
      'ROLE_ADMIN_FRANCHISE'
    );

    $this->generateUser(
      $manager,
      $generator,
      'Admin',
      'Salle',
      'AdminSalle',
      'admin@salle.fr',
      'ROLE_ADMIN_SALLE'
    );

    $this->generateUser(
      $manager,
      $generator,
      'Ouvreur',
      'Salle',
      'OuvreurSalle',
      'ouveur@salle.fr',
      'ROLE_OUVREUR'
    );

    $this->generateUser(
      $manager,
      $generator,
      'User',
      'Lambda',
      'AdminLambda',
      'user@lambda.fr',
      'ROLE_USER'
    );

    $this->generateFranchise($manager);

    $manager->flush();
  }

  // Create Franchise
  public function generateFranchise(EntityManager $em) {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < 10; $i++) {
      $franchise = new Franchise();
      $franchise->setAdmin($generator->numberBetween(1, 100));
      $franchise->setName($generator->company);
      $franchise->setPicture("default.png");
      $em->persist($franchise);
      $this->generateGym($em, $franchise);
      $this->generatePayments($em, $franchise);
    }
    $em->flush();
  }

  // Generate fake payments
  public function generatePayments(EntityManager $em, Franchise $franchise) {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < $generator->numberBetween(10, 50); $i++) {
      $type = $generator->randomElement(['route', 'gym']);
      $aDate = $generator->dateTimeBetween('-1 years', 'now');
      $payment = new Payments();
      $payment->setType($type);
      $payment->setCreatedAt($aDate);
      $payment->setUpdatedAt($aDate);
      $payment->setAmount($type === 'route' ? 100 : 1500);
      $payment->setStatus(
        $generator->randomElement(['pending', 'success', 'failed'])
      );
      $payment->setFranchise($franchise);
      $payment->setToken(null);
      $em->persist($payment);
    }
    $em->flush();
  }

  // Create Gyms
  public function generateGym(EntityManager $em, Franchise $franchise) {
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
      $gym->setPicture("default.png");
      $gym->setCreatedAt($generator->dateTimeBetween('-1 years', 'now'));
      $em->persist($gym);
      $this->generateEvents($em, $gym);
      $this->generateRoute($em, $gym);
    }
    $em->flush();
  }

  public function generateRoute(EntityManager $em, Gym $gym) {
    $generator = Factory::create('fr_FR');
    $difficulty = [
      "1", "2", "3", "4", "5a", "5b", "5c",
      "6a", "6a+", "6b", "6b+", "6c", "6c+",
      "7a", "7a+", "7b", "7b+", "7c", "7c+",
      "8a", "8a+", "8b", "8b+", "8c", "8c+",
      "9a", "9a+", "9b", "9b+", "9c"
    ];
    for ($i = 0; $i < $generator->numberBetween(3, 10); $i++) {
      $route = new Route();
      $route->setId($i + 1);
      $is_opened = $generator->numberBetween(0, 1);
      $route->setOpened($is_opened);
      $route->setGym($gym);
      $route->setPicture("default.png");
      $route->setDifficulty($difficulty[$generator->numberBetween(0, 29)]);
      $dateCreated = $generator->dateTimeBetween('-1 years', 'now');
      $route->setCreatedAt($dateCreated);
      if($is_opened === 0) {
        $route->setClosedAt($generator->dateTimeBetween($dateCreated, 'now'));
      }
      $em->persist($route);
    }
    $em->flush();
  }

  // Generate events
  public function generateEvents(EntityManager $em, Gym $gym) {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < $generator->numberBetween(1, 10); $i++) {
      $event = new Event();
      $event->setId($i + 1);

      $types = [
        'competition',
        'renforcement',
        'entrainement',
        'yoga'
      ];

      $event->setTitle($types[rand(0, sizeof($types) - 1)]);
      $event->setDescription(
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque vitae mauris odio. Sed nec enim vitae nisl commodo semper vitae eu risus. Fusce at ligula dictum"
      );
      $event->setEventDate(new \DateTime());
      $event->setEndDate($generator->dateTimeBetween('+1 hours', '+6 hours'));
      $event->setGym($gym);

      $creator = $em
            ->getRepository(User::class)
            ->find(rand(1, 101)); 

      $event->setCreator($creator);
      $em->persist($event);
    }
    $em->flush();
  }

  // Generate medias
  public function generateMedias(EntityManager $em, User $user) {
    $generator = Factory::create('fr_FR');
    for ($i = 0; $i < $generator->numberBetween(0, 10); $i++) {
      $media = new Media();
      $media->setId($i + 1);
      $media->setUserId($user);
      $media->setSource($generator->imageUrl(640, 480, 'cats'));
      $media->setType($generator->randomElement(['png', 'jpg', 'jpeg', 'gif']));
      $media->setCreatedAt(new \DateTime());
      $em->persist($media);
    }
  }

  public function generateUser(
    EntityManager $em,
    $generator,
    $fn,
    $ln,
    $nn,
    $email,
    $role
  ) {
    //Admin part
    $user = new User();
    $user->setEmail($email);
    $user->setFirstname($fn);
    $user->setLastname($ln);
    $user->setUsername($nn);
    $user->setPassword(
      '$2y$13$6lBervVYeDGuuwi5VeSv3e.H0YlWo03yNhWOgPWIA8BIHkKIC/InC'
    );
    $user->setBirthdate($generator->dateTimeBetween('-22 years', '-21 years'));
    $user->setCreatedAt(new \DateTime());
    $user->setOtp(12345);
    $user->setIsActivated(true);
    $user->setPicture('default.jpg');
    $user->setRoles([$role]);
    $em->persist($user);
  }
}