<?php

namespace App\Controller;

use App\Repository\SnowtrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function indexAction(SnowtrickRepository $repository): Response
    {
        $snowtricks = $repository->findLatest();
        return $this->render('pages/home.html.twig', [
            'snowtricks' => $snowtricks
        ]);
    }
}