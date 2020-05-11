<?php

namespace App\Controller;

use App\Entity\Token;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/forgotpassword", name="forgotpassword")
     */
    public function forgotPassword(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $userRepository->findOneByEmail($data["email"]);

            if ($user !== null) {
                $token = new Token($user);
                $user->addToken($token);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $message = (new \Swift_Message('Snowtricks - Password reset'))
                    ->setFrom('kvn.macquet.mailer@gmail.com')
                    ->setTo($data["email"])
                    ->setBody(
                        $this->render(
                            'mail/resetpassword.html.twig',
                            [
                                'token' => $token->getValue()
                            ]
                        ),
                        'text/html'
                    );
                $mailer->send($message);
            }
            $this->addFlash('success', 'email sent !');
        }
        return $this->render('security/forgotpassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/resetpassword/{value}", name="resetpassword")
     * @param Request $request
     * @param Token $token
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param TokenRepository $tokenRepository
     * @return Response
     */
    public function resetPassword(Request $request, ?Token $token = null, TokenRepository $tokenRepository, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        if ($token === null) {
            $this->addFlash('warning', 'Token invalid');
            return $this->redirectToRoute('home');
        }

        if (!$token->isValid()) {
            $this->entityManager->remove($token);
            $this->entityManager->flush();

            $this->addFlash('warning', 'Token invalid');
            return $this->redirectToRoute('home');
        }

        $user = $token->getUser();

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $tokens = $tokenRepository->findBy([
                'user' => $user
            ]);

            foreach ($tokens as $atoken) {
                $this->entityManager->remove($atoken);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Password have been changed successfully');

            return $this->redirectToRoute('login');
        }
        return $this->render('security/resetpassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
