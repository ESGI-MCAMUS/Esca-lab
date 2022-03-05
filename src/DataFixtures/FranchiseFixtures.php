<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Franchise;

class FranchiseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $franchise = new Franchise();
            $franchise->setAdmin($generator->numberBetween(1, 100));
            $franchise->setName($generator->company);
            $manager->persist($franchise);
        }
        $manager->flush();
    }
}