<?php

namespace App\Controller;

use App\Entity\Snowtrick;
use App\Repository\SnowtrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnowtrickController extends AbstractController
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
     * @route("/Snowtricks", name="snowtricks")
     * @return Response
     */
    public function indexAction(): Response
    {
        $snowtrick = $this->repository->findAllVisible();
        $snowtrick[0]->setTitle('nouveau titre');
        $this->em->flush();
        return $this->render("snowtricks/index.html.twig", [
            'current_menu' => 'snowtricks'
        ]);
    }
}