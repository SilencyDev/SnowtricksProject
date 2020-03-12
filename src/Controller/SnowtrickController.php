<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Mainpicture;
use App\Entity\picture;
use App\Entity\Snowtrick;
use App\Entity\User;
use App\Form\SnowtrickType;
use App\Form\CommentType;
use App\Repository\SnowtrickRepository;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\picture\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SnowtrickController extends AbstractController
{
    /**
     * @var SnowtrickRepository
     */
    private $snowtrickRepository;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    public function __construct(SnowtrickRepository $snowtrickRepository, CommentRepository $commentRepository, EntityManagerInterface $em)
    {
        $this->snowtrickRepository = $snowtrickRepository;
        $this->commentRepository = $commentRepository;
        $this->em = $em;
    }

    /**
     * @route("/", name="snowtricks")
     * @return Response
     */
    public function indexAction(): Response
    {
        $snowtricks = $this->snowtrickRepository->findAllVisible();
        return $this->render("snowtricks/index.html.twig", [
            'current_menu' => 'snowtricks',
            'snowtricks' => $snowtricks
        ]);
    }

    /**
     * @Route("/member/snowtrick", name="member.snowtrick.index")
     * @return Response
     */
    public function indexMemberAction(Security $security)
    {
        /** @var User $currentUser */
        $currentUser = $security->getUser();
        $snowtricks = $currentUser->getSnowtricks();

        return $this->render('member/snowtricks/index.html.twig', compact("snowtricks"));
    }

    /**
     * @Route("/admin/snowtrick", name="admin.snowtrick.index")
     * @return Response
     */
    public function indexAdminAction()
    {
        $snowtricks = $this->snowtrickRepository->findAllVisibleDesc();
        $snowtricksToValidate = $this->snowtrickRepository->findAllInvisible();
        return $this->render('admin/snowtricks/index.html.twig', compact("snowtricks","snowtricksToValidate"));
    }

    /**
     * @Route("/snowtrick/{id}", name="snowtrick.show")
     * @param Snowtrick
     * @return Response
     */
    public function show(Snowtrick $snowtrick): Response
    {   
        $newComment = new Comment();

        $form = $this->createForm(CommentType::class, $newComment, [
            'action' => $this->generateUrl('member.comment.new', [
                'id' => $snowtrick->getId()
            ])
        ]);

        $comments = $snowtrick->getComments();
        return $this->render('snowtricks/show.html.twig', [
            'snowtrick' => $snowtrick,
            'comments' => $comments,
            'form' => $form->createView(),
            'newComment' => $newComment
        ]);
    }

    /**
     * @Route("/member/snowtrick/new", name="member.snowtrick.new")
     */
    public function newAction(Security $security, Request $request) 
    {
        $snowtrick = new Snowtrick();

        $roles = $security->getUser()->getRoles();

        if(in_array("ROLE_ADMIN", $roles)) {
            $snowtrick->setValidated(true);
        } else {
            $snowtrick->setValidated(false);
        }

        $form = $this->createForm(SnowtrickType::class, $snowtrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {

            $pictures = $form->get('pictures')->getData();
            $mainpicture = $form->get('mainpicture')->getData();

            $mainUpload = new Mainpicture;

            $mainUpload->setName($mainpicture->getClientOriginalName());

            $mainpicture = $mainpicture->move(
                $this->getParameter('uploads_directory'),
                $mainUpload->getId() . '.' . $mainpicture->guessExtension()
            );

            $mainUpload->setPath('uploads/' . $mainUpload->getId() . '.' . $mainpicture->guessExtension());
            $mainUpload->setRealPath($mainpicture->getRealPath());

            $snowtrick->addMainpicture($mainUpload);
            $this->em->persist($mainUpload);

            /** @var UploadedFile $picture */
            foreach($pictures as $picture) {
                $upload = new Picture;

                $upload->setName($picture->getClientOriginalName());

                $picture = $picture->move(
                    $this->getParameter('uploads_directory'),
                    $upload->getId() . '.' . $picture->guessExtension()
                );

                $upload->setPath('uploads/' . $upload->getId() . '.' . $picture->guessExtension());
                $upload->setRealPath($picture->getRealPath());

                $snowtrick->addpicture($upload);
                $this->em->persist($upload);
            }

            $snowtrick->setAuthor($security->getUser());

            $this->em->persist($snowtrick);
            $this->em->flush();

            $this->addFlash('success', 'Created with success!');

            return $this->redirectToRoute("member.snowtrick.index");
        }
        return $this->render('member/snowtricks/new.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/member/snowtrick/{id}", name="member.snowtrick.edit", methods={"GET","POST"})
     * @param Snowtrick
     * @param Request
     * @return Response
     */
    public function editAction(Security $security, Snowtrick $snowtrick, Request $request, PictureRepository $pictureRepository)
    {
        $roles = $security->getUser()->getRoles();

        if(in_array("ROLE_ADMIN", $roles)) {
            $snowtrick->setValidated(true);
        } else {
            $snowtrick->setValidated(false);
        }

        $form = $this->createForm(SnowtrickType::class, $snowtrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {

            $pictures = $form->get('pictures')->getData();
            $mainpicture = $form->get('mainpicture')->getData();

            foreach ($snowtrick->getpictures() as $pictureToDelete) {
                unlink($pictureToDelete->getRealPath());

                $this->em->remove($pictureRepository->findOneById($pictureToDelete->getId()));
            }

            $this->em->flush();

            /** @var UploadedFile $picture */
            foreach($pictures as $picture) {
                $upload = new Picture;

                $upload->setName($picture->getClientOriginalName());

                $picture = $picture->move(
                    $this->getParameter('uploads_directory'),
                    $upload->getId() . '.' . $picture->guessExtension()
                );

                $upload->setPath('uploads/' . $upload->getId() . '.' . $picture->guessExtension());
                $upload->setRealPath($picture->getRealPath());

                $snowtrick->addpicture($upload);
                $this->em->persist($upload);
            }

            $this->em->persist($snowtrick);
            $this->em->flush();

            $this->addFlash('success', 'Edited with success!');
            
            if(in_array("ROLE_ADMIN", $roles)) {
                return $this->redirectToRoute("admin.snowtrick.index");
            } else {
                return $this->redirectToRoute("member.snowtrick.index");
            }
        }

        return $this->render('member/snowtricks/edit.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/member/snowtrick/{id}", name="member.snowtrick.delete", methods={"DELETE"})
     * @param Snowtrick
     * @param Request
     * @return Response
     */
    public function deleteAction(Snowtrick $snowtrick, Request $request, Security $security) {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();

        if(in_array("ROLE_ADMIN", $roles) || in_array("ROLE_MEMBER", $roles) && $snowtrick->getAuthor() == $username ) {
            if($this->isCsrfTokenValid('delete' . $snowtrick->getId(), $request->get('_token'))) {
                $this->em->remove($snowtrick);
                $this->em->flush();

                $this->addFlash('success', 'Deleted with success!');
                
                return $this->redirectToRoute('admin.snowtrick.index');
            }
        } else {
            $this->addFlash('warning', 'You do not have the previlege to remove this post');
            return $this->redirectToRoute('member.snowtrick.index');
        }
    }
}