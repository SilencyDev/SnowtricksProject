<?php

namespace App\Controller\Member;

use App\Entity\Snowtrick;
use App\Form\SnowtrickMemberType;
use App\Repository\SnowtrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member")
 */
class MemberSnowtrickController extends AbstractController
{
    /**
     * @var SnowtrickRepository
     */
    private $repository;

    public function __construct(SnowtrickRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/", name="member.snowtrick.index")
     * @return Response
     */
    public function indexAction()
    {
        $snowtricks = $this->repository->findMyTricks();
        return $this->render('member/snowtricks/index.html.twig', compact("snowtricks"));
    }

    /**
     * @Route("/snowtrick/create", name="member.snowtrick.new")
     */
    public function newAction(Request $request) {
        $snowtrick = new snowtrick();
        $form = $this->createForm(SnowtrickMemberType::class, $snowtrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $this->em->persist($snowtrick);
            $this->addFlash('success', 'Edited with success!');
            $this->em->flush();
            return $this->redirectToRoute("member.snowtrick.index");
        }
        return $this->render('member/snowtricks/new.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/snowtrick/{id}", name="member.snowtrick.edit", methods={"GET","POST"})
     * @param Snowtrick
     * @param Request
     * @return Response
     */
    public function editAction(Snowtrick $snowtrick, Request $request)
    {
        $form = $this->createForm(SnowtrickMemberType::class, $snowtrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $this->em->flush();
            $this->addFlash('success', 'Edited with success!');
            return $this->redirectToRoute("member.snowtrick.index");
        }
        return $this->render('member/snowtricks/edit.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/snowtrick/{id}", name="member.snowtrick.delete", methods={"DELETE"})
     * @param Snowtrick
     * @param Request
     * @return Response
     */
    public function deleteAction(Snowtrick $snowtrick, Request $request) {
        if($this->isCsrfTokenValid('delete' . $snowtrick->getId(), $request->get('_token'))) {
            $this->em->remove($snowtrick);
            $this->addFlash('success', 'Deleted with success!');
            $this->em->flush();
        }
        return $this->redirectToRoute('member.snowtrick.index');
    }
}