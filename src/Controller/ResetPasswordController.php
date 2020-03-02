<?php

namespace App\Controller;

use App\Entity\Token;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/forgotpassword", name="forgotpassword")
     */
    public function forgotPassword(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $user = $userRepository->findOneByEmail($data["email"]);

            if($user !== null) {

                $token = new Token($user);
                $user->addToken($token);

                $this->em->persist($user);
                $this->em->flush();

                $message = (new \Swift_Message('Snowtricks - Password reset'))
                    ->setFrom('kvn.macquet.mailer@gmail.com')
                    ->setTo($data["email"])
                    ->setBody(
                        $this->render(
                            'mail/resetpassword.html.twig', [
                                'token' => $token->getValue()
                            ]
                            ),
                            'text/html'
                        );
                    $mailer->send($message);

                    $this->addFlash('success', 'email sent !');
            }
        }

        return $this->render('security/forgotpassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/resetpassword/{value}", name="resetpassword")
     * @param Request $request
     * @param Token $token
     * @param UserPasswordEncoderInterface $userPasswordEncoderInterface
     * @param TokenRepository $tokenRepository
     * @return Response
     */
    public function resetPassword(Request $request, Token $token, TokenRepository $tokenRepository, UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        if ($token->isValid()) {
            $user = $token->getUser();

            $form = $this->createForm(ResetPasswordType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($token->isValid()) {
                    $user->setPassword(
                        $userPasswordEncoderInterface->encodePassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    );
                    $tokens = $tokenRepository->findBy([
                        'user' => $user
                    ]);

                    foreach ($tokens as $atoken) {
                        $this->em->remove($atoken);
                    }

                    $this->em->flush();

                    $this->addFlash('success', 'Password have been changed successfully');

                    return $this->redirectToRoute('login');

                } else {
                    $this->addFlash('warning', "your link is no longer valid");

                    $this->em->remove($atoken);
                    $this->em->flush();
                }
            }
            return $this->render('security/resetpassword.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->addFlash('warning', 'Token invalid');
            return $this->redirectToRoute('Home');
        }
    }
}