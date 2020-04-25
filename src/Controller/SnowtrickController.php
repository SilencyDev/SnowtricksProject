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


    public function __construct(SnowtrickRepository $snowtrickRepository, EntityManagerInterface $em)
    {
        $this->snowtrickRepository = $snowtrickRepository;
        $this->em = $em;
    }

    /**
     * @Route("/mytricks", name="snowtrick.mytrick")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
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
     * @Route("/trickmanager", name="snowtrick.manager")
     * @IsGranted ({"ROLE_ADMIN"})
     * @return Response
     */
    public function indexAdminAction()
    {
        $snowtricks = $this->snowtrickRepository->findAllVisibleDesc();
        $snowtricksToValidate = $this->snowtrickRepository->findAllInvisible();
        return $this->render('admin/snowtricks/index.html.twig', compact("snowtricks","snowtricksToValidate"));
    }

    /**
     * @Route("/show/{id}", name="snowtrick.show")
     * @param Snowtrick
     * @return Response
     */
    public function show(Snowtrick $snowtrick, CommentRepository $commentRepository): Response
    {   
        $newComment = new Comment();

        $form = $this->createForm(CommentType::class, $newComment, [
            'action' => $this->generateUrl('comment.new', [
                'id' => $snowtrick->getId()
            ])
        ]);

        $comments = $commentRepository->findBy([], ['id' => 'DESC'], 3, 0);
        return $this->render('snowtricks/show.html.twig', [
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

            return $this->redirectToRoute("snowtrick.mytrick");
        }

        return $this->render('member/snowtricks/_form.html.twig', [
            'form' => $form->createView(),
            'path' => 'snowtrick.new',
        ]);
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
        } else {
            $snowtrick->setValidated(false);
        }

        if ($form->isSubmitted() && $form->isvalid()) {

            $pictures = $form->get('pictures')->getData();
            $mainpicture = $form->get('mainpicture')->getData();
            $videos = $form->get('videos')->getData();

            if ($mainpicture !== NULL) {

                if ($snowtrick->getMainpicture() !== null) {
                    unlink($snowtrick->getMainpicture()->getRealPath());
                    $this->em->remove($snowtrick->getMainpicture());
                    $this->em->flush();
                }
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
            $snowtrick->setUpdatedAt();

            $this->em->persist($snowtrick);
            $this->em->flush();

            $this->addFlash('success', 'Edited with success!');
            
            if(in_array("ROLE_ADMIN", $roles)) {
                return $this->redirectToRoute("snowtrick.manager");
            } else {
                return $this->redirectToRoute("snowtrick.mytrick");
            }
        }

        return $this->render('member/snowtricks/_form.html.twig', [
            'snowtrick' => $snowtrick,
            'form' => $form->createView(),
            'path' => 'snowtrick.edit'
        ]);
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
                $this->em->remove($snowtrick);
                $this->em->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }

    /**
     * @Route("/picture/{id}", name="picture.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Picture
     * @param Request
     * @return Response
     */
    public function deletePictureAction(Picture $picture, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($picture->getSnowtrick()->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $picture->getId(), $token)) {
                $this->em->remove($picture);
                $this->em->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }

    /**
     * @Route("/mainpicture/{id}", name="mainpicture.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Mainpicture
     * @param Request
     * @return Response
     */
    public function deleteMainPictureAction(Mainpicture $picture, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($picture->getSnowtrick()->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $picture->getId(), $token)) {
                unlink($picture->getRealPath());
                $this->em->remove($picture);
                $this->em->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data //
            }
        }
        return new JsonResponse(null, 400);
    }

    /**
     * @Route("/video/delete/{id}", name="video.delete")
     * @IsGranted ({"ROLE_ADMIN", "ROLE_MEMBER"})
     * @param Video
     * @param Request
     * @return Response
     */
    public function deleteVideoAction(Video $video, Request $request, Security $security)
    {

        $roles = $security->getUser()->getRoles();
        $username = $security->getUser()->getUsername();
        $token = json_decode($request->getContent(), true)['token']??null;
        if (in_array("ROLE_ADMIN", $roles) || ($video->getSnowtrick()->getAuthor() == $username)) {
            if ($this->isCsrfTokenValid('delete' . $video->getId(), $token)) {
                $this->em->remove($video);
                $this->em->flush();

                return new JsonResponse(array('message' => 'Deleted with success!'), 201); // 200 data // 204 null
            }
        }
        return new JsonResponse(null, 400);
    }
}