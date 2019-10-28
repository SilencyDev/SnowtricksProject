<?php

namespace App\Controller\Admin;

use App\Entity\Snowtrick;
use App\Form\SnowtrickType;
use App\Repository\SnowtrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSnowtrickController extends AbstractController
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
     * @Route("/admin", name="admin.snowtrick.index")
     * @return Response
     */
    public function indexAction()
    {
        $snowtricks = $this->repository->findAll();
        return $this->render('admin/snowtricks/index.html.twig', compact("snowtricks"));
    }

    /**
     * @Route("/admin/snowtrick/create", name="admin.snowtrick.new")
     */
    public function newAction(Request $request) {
        $snowtrick = new snowtrick();
        $form = $this->createForm(SnowtrickType::class, $snowtrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $this->em->persist($snowtrick);
            $this->em->flush();
            return $this->redirectToRoute("admin.snowtrick.index");
        }
        return $this->render('admin/snowtricks/new.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/snowtrick/{id}", name="admin.snowtrick.edit", methods={"GET","POST"})
     * @param Snowtrick
     * @param Request
     * @return Response
     */
    public function editAction(Snowtrick $snowtrick, Request $request)
    {
        $form = $this->createForm(SnowtrickType::class, $snowtrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $this->em->flush();
            $this->addFlash('success', 'Edited with success!');
            return $this->redirectToRoute("admin.snowtrick.index");
        }
        return $this->render('admin/snowtricks/edit.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/snowtrick/{id}", name="admin.snowtrick.delete", methods={"DELETE"})
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
        return $this->redirectToRoute('admin.snowtrick.index');
    }
}