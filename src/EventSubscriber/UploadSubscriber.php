<?php

namespace App\EventSubscriber;

use App\Entity\Snowtrick;
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
        if (!$entity instanceof Snowtrick) {
            return;
        }

        if ($entity->getPictures() !== NULL) {
            foreach ($entity->getPictures() as $picture) {
                unlink($picture->getRealPath());
            }
        }

        if ($entity->getMainpicture() !== NULL) {
            unlink($entity->getMainpicture()->getRealPath());
        }
        return;
    }
}
