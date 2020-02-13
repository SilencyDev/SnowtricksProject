<?php

namespace App\EventSubscriber;

use App\Entity\File;
use App\Entity\Snowtrick;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class UploadSubscriber implements EventSubscriber
{
    private $uploadPath;

    public function __construct(string $uploadPath) 
    {
        $this->uploadPath = $uploadPath;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::preUpdate
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

    public function preUpdate(LifecycleEventArgs $args) 
    {
    }
}