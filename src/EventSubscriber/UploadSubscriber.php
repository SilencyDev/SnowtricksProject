<?php

namespace App\EventSubscriber;

use App\Entity\Snowtrick;
use App\Entity\User;
use App\Entity\UserPicture;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class UploadSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            if ($entity->getPicture() !== NULL && file_exists($entity->getPicture()->getRealPath())) {
                unlink($entity->getPicture()->getRealPath());
            }
        }

        if (!$entity instanceof Snowtrick) {
            return;
        }

        foreach ($entity->getPictures() as $picture) {
            unlink($picture->getRealPath());
        }

        if ($entity->getMainpicture() !== NULL && file_exists($entity->getMainpicture()->getRealPath())) {
            unlink($entity->getMainpicture()->getRealPath());
        }
        return;
    }
}
