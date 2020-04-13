<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Snowtrick;
use App\Form\CommentType;
use App\Repository\SnowtrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentController extends AbstractController
{
    /**
     * @var SnowtrickRepository
     */
    private $snowtrickRepository;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    public function __construct(SnowtrickRepository $snowtrickRepository, CommentRepository $commentRepository, EntityManagerInterface $em)
    {
        $this->snowtrickRepository = $snowtrickRepository;
        $this->commentRepository = $commentRepository;
        $this->em = $em;
    }

    /**
     * @Route("/member/comment{id}/new", name="member.comment.new"), methods={"GET","POST"})
     * @param Snowtrick
     * @param Comment
     */
    public function newAction(Security $security, Request $request, Snowtrick $snowtrick)
    {
        $newComment = new Comment;

        $roles = $security->getUser()->getRoles();

        if (in_array("ROLE_ADMIN", $roles)) {
            $newComment->setValidated(true);
        } else {
            $newComment->setValidated(false);
        }

        $form = $this->createForm(CommentType::class, $newComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $newComment = $form->getData();
            $newComment->setAuthor($security->getUser());
            $newComment->setSnowtrick($snowtrick);

            $this->em->persist($newComment);
            $this->em->flush();

            $this->addFlash('success', 'Created with success!');

            return $this->render('row/addcomment.html.twig', [
                'acomment' => $newComment,
                'form' => $form->createView(),
            ]);
        } else {
            $this->addFlash('warning', 'Error');

            return $this->render('snowtricks/_form.html.twig', [
                'form' => $form->createView(),
                'path' => 'member.comment.new'
            ]);
        }
    }

    /**
     * @Route("/member/comment/delete/{id}", name="member.comment.delete")
     * @param Comment
     * @param Request
     * @return Response
     */
    public function deleteCommentAction(Comment $comment, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($comment->getSnowtrick->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $comment->getId(), $token)) {
                $this->em->remove($comment);
                $this->em->flush();

                return new JsonResponse(null, 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }
}
