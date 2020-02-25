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
        foreach ($entity->getFiles() as $file) {
            unlink($file->getRealPath());
        };
    }
}