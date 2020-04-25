<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignUpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/signup", name="signup", methods={"GET","POST"})
     */
    public function signup(Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $user = new User;

        $form = $this->createForm(SignUpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $userPasswordEncoder->encodePassword($user, $user->getPassword());
            
            $user->setPassword($encodedPassword);
            $this->entityManager->persist($user);
            $this->addFlash('success', 'Account created with success !');
            $this->entityManager->flush();

            return $this->redirectToRoute("login");
        }
        return $this->render('security/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
