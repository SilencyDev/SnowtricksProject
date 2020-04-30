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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Added with success!');

            return $this->redirectToRoute('category.index');
        }

        return $this->render('admin/category/_form.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'path' => 'category.new',
        ],new Response("",400));
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
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'Edited with success!');

            return $this->redirectToRoute('category.index');
        }

        return $this->render('admin/category/_form.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'path' => 'category.edit',
        ],new Response("",400));
    }

    /**
     * @Route("/delete/{id}", name="category.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Category
     * @param Request
     * @return Response
     */
    public function deleteCategoryAction(Category $category, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles)) {
            if ($this->isCsrfTokenValid('delete' . $category->getId(), $token)) {
                $this->entityManager->remove($category);
                $this->entityManager->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }
}
