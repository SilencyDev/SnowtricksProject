<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
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
    public function indexAction(SnowtrickRepository $snowtrickRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'snowtricks' => $snowtrickRepository->findBy(['validated' => true], ['id' => 'DESC'], 3, 0),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/loadmore/snowtrick", name="loadmore")
     *
     * @param SnowtrickRepository $snowtrickRepository
     * @param integer $page
     * @return Response
     */
    public function loadMoreAction(SnowtrickRepository $snowtrickRepository, Request $request): Response
    {
        $page = (int) $request->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        
        return $this->render('loadmore/snowtrick.html.twig', [
            'snowtricks' => $snowtrickRepository->findBy(['validated' => true], ['id' => 'DESC'], 3, ($page-1) * 3),
        ]);
    }

    /**
     * @Route("/search", name="home.search")
     * @return Response
     */
    public function searchAction(SnowtrickRepository $snowtrickRepository, Request $request,  CategoryRepository $categoryRepository): Response
    {
        $category = (string) $request->get('category');
        
        return $this->render('pages/home.html.twig', [
            'snowtricks' => $snowtrickRepository->findByCategory($category, 3, 0),
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
