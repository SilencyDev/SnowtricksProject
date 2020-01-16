<?php

namespace App\Controller;

use App\Entity\Snowtrick;
use App\Repository\SnowtrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/snowtricks")
 */
class SnowtrickController extends AbstractController
{
    /**
     * @var SnowtrickRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(SnowtrickRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;

    }

    /**
     * @route("/", name="snowtricks")
     * @return Response
     */
    public function indexAction(): Response
    {
        $snowtricks = $this->repository->findAllVisible();
        return $this->render("snowtricks/index.html.twig", [
            'current_menu' => 'snowtricks',
            'snowtricks' => $snowtricks
        ]);
    }

    /**
     * @Route("/{id}", name="snowtrick.show")
     * @param Snowtrick
     * @return Response
     */
    public function show(Snowtrick $snowtrick): Response
    {
        return $this->render('snowtricks/show.html.twig', [
            'current_menu' => 'snowtricks',
            'snowtrick' => $snowtrick
        ]);
    }
}