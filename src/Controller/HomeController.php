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
        $page = (int) $request->get('page', 1);
        if ($page === 0) {
            $page = 1;
        }
        return $this->render('pages/home.html.twig', [
            'snowtricks' => $snowtrickRepository->findBy([], ['id' => 'DESC'], $page * 3, 0),
            'page' => $page,
        ]);
    }
}
