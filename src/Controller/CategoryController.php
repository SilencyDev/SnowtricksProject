<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="category.index")
     * @IsGranted ({"ROLE_ADMIN"})
     */
    public function indexAction(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="category.new")
     * @IsGranted ({"ROLE_ADMIN"})
     */
    public function newAction(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            
            $this->addFlash('success', 'Added with success!');

            return $this->redirectToRoute('category.index');
        }

        return $this->render('admin/category/_form.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="category.edit", methods={"GET","POST"})
     * @IsGranted ({"ROLE_ADMIN"})
     * @param Category
     * @param Request
     * @return Response
     */
    public function editAction(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($form->get('name')->getData());
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', 'Edited with success!');

            return $this->redirectToRoute('category.index');
        }

        return $this->render('admin/category/_form.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="category.delete", methods={"DELETE"})
     * @IsGranted ({"ROLE_ADMIN"})
     */
    public function deleteAction(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'Deleted with success!');
        }

        return $this->redirectToRoute('category.index');
    }
}
