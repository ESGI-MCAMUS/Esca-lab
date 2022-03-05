<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;

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

        $manager->flush();
    }
}