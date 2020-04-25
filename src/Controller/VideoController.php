<?php

namespace App\Controller;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class VideoController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/video/delete/{id}", name="video.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Video
     * @param Request
     * @return Response
     */
    public function deleteVideoAction(Video $video, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($video->getSnowtrick()->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $video->getId(), $token)) {
                $this->entityManager->remove($video);
                $this->entityManager->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data // 204 null
            }
        }
        return new JsonResponse(null, 400);
    }
}