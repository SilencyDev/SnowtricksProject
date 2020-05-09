<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Snowtrick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\SnowtrickType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\SnowtrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\picture\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/snowtrick")
 */
class SnowtrickController extends AbstractController
{
    /**
     * @var SnowtrickRepository
     */
    private $snowtrickRepository;


    public function __construct(SnowtrickRepository $snowtrickRepository, EntityManagerInterface $entityManager)
    {
        $this->snowtrickRepository = $snowtrickRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mytricks", name="snowtrick.mytrick")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @return Response
     */
    public function myTricksAction(Security $security)
    {
        /** @var User $currentUser */
        $currentUser = $security->getUser();
        $snowtricks = $currentUser->getSnowtricks();

        return $this->render('member/snowtricks/index.html.twig', [
            "snowtricks" => $snowtricks,
        ]);
    }

    /**
     * @Route("/trickmanager", name="snowtrick.manager")
     * @IsGranted ({"ROLE_ADMIN"})
     * @return Response
     */
    public function trickManagerAction()
    {
        $snowtricks = $this->snowtrickRepository->findAllVisibleDesc();
        $snowtricksToValidate = $this->snowtrickRepository->findAllInvisible();
        return $this->render('admin/snowtricks/index.html.twig', [
            "snowtricks" => $snowtricks,
            "snowtricksToValidate" => $snowtricksToValidate,
        ]);
    }

    /**
     * @Route("/show/{id}", name="snowtrick.show")
     * @param Snowtrick
     * @return Response
     */
    public function indexAction(Snowtrick $snowtrick, CommentRepository $commentRepository): Response
    {   
        $newComment = new Comment();

        $form = $this->createForm(CommentType::class, $newComment, [
            'action' => $this->generateUrl('comment.new', [
                'id' => $snowtrick->getId()
            ])
        ]);

        $comments = $commentRepository->findBySnowtrick($snowtrick->getId(), 3, 0);
        return $this->render('snowtrick/index.html.twig', [
            'snowtrick' => $snowtrick,
            'comments' => $comments,
            'form' => $form->createView(),
            'newComment' => $newComment,
            'path' => 'comment.new',
        ]);
    }

    /**
     * @Route("/new", name="snowtrick.new", methods={"POST","GET"})
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     */
    public function newAction(Security $security, Request $request) 
    {
        $snowtrick = new Snowtrick();

        $roles = $security->getUser()->getRoles();

        if(in_array("ROLE_ADMIN", $roles)) {
            $snowtrick->setValidated(true);
        }

        $form = $this->createForm(SnowtrickType::class, $snowtrick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {

            $pictures = $form->get('file')->getData();
            $mainpicture = $form->get('mainFile')->getData();
            $videos = $form->get('videos')->getData();

            if ($mainpicture !== null) {
                $mainUpload = new Picture;

                $mainUpload->setIsMainPicture(true);
                $mainUpload->setName($mainpicture->getClientOriginalName());

                $mainpicture = $mainpicture->move(
                $this->getParameter('uploads_directory'),
                $mainUpload->getId() . '.' . $mainpicture->guessExtension()
                );

                $mainUpload->setPath('uploads/' . $mainUpload->getId() . '.' . $mainpicture->guessExtension());
                $mainUpload->setRealPath($mainpicture->getRealPath());

                $snowtrick->setMainpicture($mainUpload);
                $this->entityManager->persist($mainUpload);
            }

            if ($videos !== null) {
                foreach ($videos as $video) {
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $match)) {
                        $videoUpload = new Video;
                        
                        $videoId = $match[1];
                        $videoUpload->setUrl('https://www.youtube.com/embed/' . $videoId);
                        
                        $snowtrick->addVideo($videoUpload);
                        $this->entityManager->persist($videoUpload);
                    }
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
                    $this->entityManager->persist($upload);
                }
            }

            $snowtrick->setAuthor($security->getUser());

            $this->entityManager->persist($snowtrick);
            $this->entityManager->flush();

            $this->addFlash('success', 'Created with success!');

            return $this->redirectToRoute("snowtrick.mytrick");
        }

        return $this->render('member/snowtricks/_form.html.twig', [
            'form' => $form->createView(),
            'path' => 'snowtrick.new',
        ], new Response("",400));
    }

    /**
     * @Route("/edit/{id}", name="snowtrick.edit", methods={"GET","POST"})
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Snowtrick
     * @param Request
     * @return Response
     */
    public function editAction(Security $security, Snowtrick $snowtrick, Request $request)
    {
        $roles = $security->getUser()->getRoles();

        $form = $this->createForm(SnowtrickType::class, $snowtrick);
        $form->handleRequest($request);

        if(in_array("ROLE_ADMIN", $roles) && $form->get('validated')->getData()) {
            $snowtrick->setValidated(true);
        }

        if ($form->isSubmitted() && $form->isvalid()) {

            $pictures = $form->get('file')->getData();
            $mainpicture = $form->get('mainFile')->getData();
            $videos = $form->get('videos')->getData();

            if ($mainpicture !== NULL) {

                if ($snowtrick->getMainpicture() !== null) {
                    unlink($snowtrick->getMainpicture()->getRealPath());
                    $this->entityManager->remove($snowtrick->getMainpicture());
                    $this->entityManager->flush();
                }
                $mainUpload = new Picture;

                $mainUpload->setIsMainPicture(true);
                $mainUpload->setName($mainpicture->getClientOriginalName());
    
                $mainpicture = $mainpicture->move(
                    $this->getParameter('uploads_directory'),
                    $mainUpload->getId() . '.' . $mainpicture->guessExtension()
                );
    
                $mainUpload->setPath('uploads/' . $mainUpload->getId() . '.' . $mainpicture->guessExtension());
                $mainUpload->setRealPath($mainpicture->getRealPath());
    
                $snowtrick->setMainpicture($mainUpload);
                $this->entityManager->persist($mainUpload);
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
                    $this->entityManager->persist($upload);
                }
            }

            if ($videos !== null) {
                foreach ($videos as $video) {
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $match)) {
                        $videoUpload = new Video;

                        $videoId = $match[1];
                        $videoUpload->setUrl('https://www.youtube.com/embed/' . $videoId);
                        
                        $snowtrick->addVideo($videoUpload);
                        $this->entityManager->persist($videoUpload);
                    }
                }
            }
            $snowtrick->setUpdatedAt();

            $this->entityManager->persist($snowtrick);
            $this->entityManager->flush();

            $this->addFlash('success', 'Edited with success!');
            
            if(in_array("ROLE_ADMIN", $roles)) {
                return $this->redirectToRoute("snowtrick.manager");
            }
            return $this->redirectToRoute("snowtrick.mytrick");
        }

        return $this->render('member/snowtricks/_form.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView(),
            'path' => 'snowtrick.edit'
        ], new Response("",400));
    }

    /**
     * @Route("/delete/{id}", name="snowtrick.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
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
                $this->entityManager->remove($snowtrick);
                $this->entityManager->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }
}