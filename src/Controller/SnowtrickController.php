<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Mainpicture;
use App\Entity\Picture;
use App\Entity\Snowtrick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\SnowtrickType;
use App\Form\CommentType;
use App\Repository\SnowtrickRepository;
use App\Repository\CommentRepository;
use App\Repository\MainpictureRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            $videos = $form->get('videos')->getData();

            if ($mainpicture !== null) {
                $mainUpload = new Mainpicture;

                $mainUpload->setName($mainpicture->getClientOriginalName());

                $mainpicture = $mainpicture->move(
                $this->getParameter('uploads_directory'),
                $mainUpload->getId() . '.' . $mainpicture->guessExtension()
                );

                $mainUpload->setPath('uploads/' . $mainUpload->getId() . '.' . $mainpicture->guessExtension());
                $mainUpload->setRealPath($mainpicture->getRealPath());

                $snowtrick->setMainpicture($mainUpload);
                $this->em->persist($mainUpload);
            }

            if ($videos !== null) {
                foreach ($videos as $video) {
                    $videoUpload = new Video;

                    $videoUpload->setUrl($video);

                    $snowtrick->addVideo($videoUpload);
                    $this->em->persist($videoUpload);
                }
            }

            if ($pictures !== null) {
                /** @var UploadedFile $picture */
                foreach ($pictures as $picture) {
                    $upload = new Picture;

                    $upload->setName($picture->getClientOriginalName());

                    $picture = $picture->move(
                    $this->getParameter('uploads_directory'),
                    $upload->getId() . '.' . $picture->guessExtension()
                    );

                    $upload->setPath('uploads/' . $upload->getId() . '.' . $picture->guessExtension());
                    $upload->setRealPath($picture->getRealPath());

                    $snowtrick->addPicture($upload);
                    $this->em->persist($upload);
                }
            }

            $snowtrick->setAuthor($security->getUser());

            $this->em->persist($snowtrick);
            $this->em->flush();

            $this->addFlash('success', 'Created with success!');

            return $this->redirectToRoute("member.snowtrick.index");
        }
        return $this->render('member/snowtricks/_form.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/member/snowtrick/edit/{id}", name="member.snowtrick.edit", methods={"GET","POST"})
     * @param Snowtrick
     * @param Request
     * @return Response
     */
    public function editAction(Security $security, Snowtrick $snowtrick, Request $request)
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
            $videos = $form->get('videos')->getData();

            if ($mainpicture !== NULL) {
                $mainUpload = new Mainpicture;

                $mainUpload->setName($mainpicture->getClientOriginalName());
    
                $mainpicture = $mainpicture->move(
                    $this->getParameter('uploads_directory'),
                    $mainUpload->getId() . '.' . $mainpicture->guessExtension()
                );
    
                $mainUpload->setPath('uploads/' . $mainUpload->getId() . '.' . $mainpicture->guessExtension());
                $mainUpload->setRealPath($mainpicture->getRealPath());
    
                $snowtrick->setMainpicture($mainUpload);
                $this->em->persist($mainUpload);
            }

            if ($pictures !== null) {
                /** @var UploadedFile $picture */
                foreach ($pictures as $picture) {
                    $upload = new Picture;

                    $upload->setName($picture->getClientOriginalName());

                    $picture = $picture->move(
                    $this->getParameter('uploads_directory'),
                    $upload->getId() . '.' . $picture->guessExtension()
                );

                    $upload->setPath('uploads/' . $upload->getId() . '.' . $picture->guessExtension());
                    $upload->setRealPath($picture->getRealPath());

                    $snowtrick->addPicture($upload);
                    $this->em->persist($upload);
                }
            }

            if ($videos !== null) {
                foreach ($videos as $video) {
                    $videoUpload = new Video;

                    $videoUpload->setUrl($video);

                    $snowtrick->addVideo($videoUpload);
                    $this->em->persist($videoUpload);
                }
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
     * @Route("/member/snowtrick/delete/{id}", name="member.snowtrick.delete")
     * @param Snowtrick 
     * @param Request
     * @return Response
     */
    public function deleteSnowtrickAction(Snowtrick $snowtrick, Request $request, Security $security) {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if(in_array("ROLE_ADMIN", $roles) || ($snowtrick->getAuthor() == $username) ) {
            if($this->isCsrfTokenValid('delete' . $snowtrick->getId(), $token)) {
                $this->em->remove($snowtrick);
                $this->em->flush();

                $this->addFlash('success', 'Deleted with success!');

                return new JsonResponse(null, 204); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }

    /**
     * @Route("/member/video/picture/{id}", name="member.picture.delete")
     * @param Picture
     * @param Request
     * @return Response
     */
    public function deletePictureAction(Picture $picture, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($picture->getSnowtrick->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $picture->getId(), $token)) {
                $this->em->remove($picture);
                $this->em->flush();

                return new JsonResponse(null, 204); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }

    /**
     * @Route("/member/video/delete/{id}", name="member.video.delete")
     * @param Video
     * @param Request
     * @return Response
     */
    public function deleteVideoAction(Video $video, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($video->getSnowtrick->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $video->getId(), $token)) {
                $this->em->remove($video);
                $this->em->flush();

                return new JsonResponse(null, 204); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }
}