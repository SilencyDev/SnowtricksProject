<?php

namespace App\Controller;

use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/picture")
 */
class PictureController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/delete/{id}", name="picture.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Picture
     * @param Request
     * @return Response
     */
    public function deletePictureAction(Picture $picture, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($picture->getSnowtrick()->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $picture->getId(), $token)) {
                $this->entityManager->remove($picture);
                $this->entityManager->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }

    /**
     * @Route("/main/delete/{id}", name="mainpicture.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Picture
     * @param Request
     * @return Response
     */
    public function deleteMainPictureAction(Picture $picture, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($picture->getSnowtrick()->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $picture->getId(), $token)) {
                unlink($picture->getRealPath());
                $this->entityManager->remove($picture);
                $this->entityManager->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }

    /**
     * @Route("/user/delete/{id}", name="userpicture.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Picture
     * @param Request
     * @return Response
     */
    public function deleteUserPictureAction(Picture $picture, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = $request->get("_token");
        if (in_array(["ROLE_ADMIN","ROLE_MEMBER"], $roles) || ($picture->getUser()->getUsername() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $picture->getId(), $token)) {
                unlink($picture->getRealPath());
                $this->entityManager->remove($picture);
                $this->entityManager->flush();

                return $this->redirectToRoute("user.profile");
            }
        }
        return $this->redirectToRoute("home");
    }
}