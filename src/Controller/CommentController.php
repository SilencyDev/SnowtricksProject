<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Snowtrick;
use App\Form\CommentType;
use App\Repository\SnowtrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            return $this->redirectToRoute("snowtrick.show", [
                'id' => $snowtrick->getId()]);
        } else {
            $this->addFlash('warning', 'Error');
            return $this->redirectToRoute("snowtrick.show", [
                'id' => $snowtrick->getId()]);
        }
    }
}
