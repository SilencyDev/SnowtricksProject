<?php

namespace App\Tests\Entity;

use App\Entity\Snowtrick;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SnowtrickTest extends KernelTestCase
{
    public function getEntity() :Snowtrick
    {
        return (new Snowtrick())
            ->setAuthor(new User)
            ->setDescription('Lorem ipsum')
            ->setTitle("A test")
            ;
    }

    public function assertHasError(Snowtrick $snowtrick, int $number = 0) 
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($snowtrick);
        $this->assertCount($number, $error);
    }

    public function testValidEntity() 
    {
        $this->assertHasError($this->getEntity(), 0);
    }

    public function testUnvalidEntity()
    {
        $this->assertHasError($this->getEntity()->setDescription(""), 1);
    }
}