<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Snowtrick;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentTest extends KernelTestCase
{
    public function getEntity() :Comment
    {
        return (new Comment())
            ->setAuthor(new User)
            ->setContent('1')
            ->setSnowtrick(new Snowtrick)
            ;
    }

    public function assertHasError(Comment $comment, int $number = 0) 
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($comment);
        $this->assertCount($number, $error);
    }

    public function testValidEntity() 
    {
        $this->assertHasError($this->getEntity(), 0);
    }

    public function testUnvalidEntity()
    {
        $this->assertHasError($this->getEntity()->setContent(""), 1);
    }
}