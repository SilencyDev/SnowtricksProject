<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserPicture;
use App\Form\UserPasswordType;
use App\Form\UserPictureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/user")
 * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
 */
Class UserController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="user.index")
     */
    public function indexAction(Request $request, UserPasswordEncoderInterface $userPasswordEncoderInterface, Security $security)
    {
         /** @var User $user */
         $user = $security->getUser();

         $form = $this->createForm(UserPasswordType::class, $user);
         $form->handleRequest($request);

         $form2 = $this->createForm(UserPictureType::class, $user);
         $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoderInterface->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->em->flush();
        }
        if ($form2->isSubmitted() && $form2->isValid()) {
            $picture = $form2->get('picture')->getData();
            if ($picture !== NULL) {
                if ($user->getPicture() !== null) {
                    unlink($user->getPicture()->getRealPath());
                    $this->em->remove($user->getPicture());
                    $this->em->flush();
                }
                $upload = new UserPicture;

                $upload->setName($picture->getClientOriginalName());

                $picture = $picture->move(
                $this->getParameter('uploads_directory'),
                $upload->getId() . '.' . $picture->guessExtension()
                );

                $upload->setPath('uploads/' . $upload->getId() . '.' . $picture->guessExtension());
                $upload->setRealPath($picture->getRealPath());

                $user->setPicture($upload);
                $this->em->persist($user);

                $this->em->flush();
            }
        }


        return $this->render('member/index.html.twig',[
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/changepassword", name="user.changepassword")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $userPasswordEncoderInterface, Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoderInterface->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
        }

        return $this->render('member/index.html.twig');
    }

    /**
     * @Route("/changepicture", name="user.changepicture")
     */
    public function changePicture(Request $request, Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();

        $form = $this->createForm(UserPictureType::class, $user);
        $form->handleRequest($request);

        $picture = $form->get('picture')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $upload = new UserPicture;

            $upload->setName($picture->getClientOriginalName());

            $picture = $picture->move(
            $this->getParameter('uploads_directory'),
            $upload->getId() . '.' . $picture->guessExtension()
            );

            $upload->setPath('uploads/' . $upload->getId() . '.' . $picture->guessExtension());
            $upload->setRealPath($picture->getRealPath());

            $user->setPicture($upload);
            $this->em->persist($upload);

            $this->em->flush();
        }

        return $this->render('member/index.html.twig');
    }
}