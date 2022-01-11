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
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}