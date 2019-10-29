<?php

namespace App\DataFixtures;

use App\Entity\Snowtrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SnowtrickFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {   for($i = 0; $i < 10; $i++) {
            $snowtrick = new Snowtrick();
            $snowtrick
                ->setAuthor("Silencydev")
                ->setDescription("Description".$i)
                ->setTitle("Title".$i)
                ->setValidated(true)
                ->setGroupe("Groupe".$i);
            $manager->persist($snowtrick);
        }
        $manager->flush();
    }
}
