<?php

namespace App\Controller;

use App\Repository\SnowtrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function indexAction(SnowtrickRepository $snowtrickRepository, Request $request): Response
    {
        return $this->render('pages/home.html.twig', [
            'snowtricks' => $snowtrickRepository->findBy([], ['id' => 'DESC'], 3, 0)
        ]);
    }

    /**
     * @Route("/{snowtrick<\d+>?3}", name="loadmore")
     *
     * @param SnowtrickRepository $snowtrickRepository
     * @param integer $trick
     * @return Response
     */
    public function loadMoreAction(SnowtrickRepository $snowtrickRepository, $snowtrick = 3): Response
    {
        return $this->render('pages/loadmore.html.twig', [
            'snowtricks' => $snowtrickRepository->findBy([], ['id' => 'DESC'], 3, $snowtrick),
        ]);
    }
}
