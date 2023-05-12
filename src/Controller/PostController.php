<?php

namespace App\Controller;

use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Post;
use App\Entity\Postcomment;
use App\Form\PostType;
use App\Form\PostcommentType;
use App\Repository\PostcommentRepository;
use App\Repository\PostRepository;
use App\Service\CommentNotificationService;
use ContainerEahXn6A\getMercuryseriesFlashy_FlashyNotifierService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use FOS\UserBundle\EventListener\FlashListener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/Post')]

class PostController extends AbstractController
{
    #[Route('/', name: 'homepage')]

    public function indexAction(ManagerRegistry $doctrine, Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $posts=$em->getRepository(Post::class)->findAll();
        return $this->render('base.html.twig', array(
            "posts" =>$posts
        ));
    }
    #[Route('/home', name: 'home')]

    public function home(ManagerRegistry $doctrine, Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $posts=$em->getRepository(Post::class)->findAll();
        return $this->render('base2.html.twig', array(
            "posts" =>$posts
        ));
    }
    #[Route('/addpost', name: 'Create_post', methods: ["GET","POST"])]

    public function addAction(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('photo')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->getClientOriginalExtension();
                $imageFile->move(
                    $this->getParameter('app.photos_directory'),
                    $newFilename
                );
                $post->setPhoto($newFilename);
            }
              if ($this->getUser()->getRoleperm()== 'patient'){
                     $post->setCreator($this->getUser());
              }
              if ($this->getUser()->getRoleperm()=='doctor'){
                $post->setCreatordoc($this->getUser());
                }
            $post->setPostdate(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'THANK YOU FOR YOUR POST , HOPE YOU GET THE ANSWER YOU ARE FOR');
        }
        return $this->render('Post/add.html.twig', [
            "Form" => $form->createView(),
            'post' => $post,

        ]);
    }
    #[Route('/list_post', name: 'list_post')]

    public function listpostAction(Request $request, PostRepository $PostRepository, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)->findAll();
        $posts = $paginator->paginate(
        $posts, /* query NOT result */
        $request->query->getInt('page', 1)/*page number*/,
            3/*limit per page*/
        );
        return $this->render('Post/list.html.twig', [
            "posts" => $posts,
        ]);
    }

  
    #[Route('/update_post/{id}', name: 'update_post')]

    public function updatepostAction(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('photo')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->getClientOriginalExtension();
                $imageFile->move(
                    $this->getParameter('app.photos_directory'),
                    $newFilename
                );
                $post->setPhoto($newFilename);
            }
            $post->setPostdate(new \DateTime('now'));
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('list_post');
        }
        return $this->render('Post/update.html.twig', [
            "form" => $form->createView(),
        ]);
    }
    #[Route('/delete_post/{id}', name: 'delete_post')]

    public function deletepostAction(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('list_post');
    }
    #[Route('/detailed_post/{id}', name: 'detailed_post')]

    public function showdetailedAction($id,Request $request, UserInterface $user,CommentNotificationService $notificationService): Response
    {   
        $comment = new Postcomment();
                $form = $this->createForm(PostcommentType::class, $comment);
                $form->handleRequest($request);
                $entityManageree=$this->getDoctrine()->getManager();
                $post = $entityManageree->getRepository(Post::class)->find($id);
                if ($form->isSubmitted() && $form->isValid()) {
                 if ($this->getUser()->getRoleperm()== 'patient'){
                    $comment->setUser($user);
                    }
                 if ($this->getUser()->getRoleperm()=='doctor'){
                    $comment->setdoctoruser($user);
                 }
                    $comment->setPost($post);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($comment);
                    $entityManager->flush();

                    $this->addFlash('info', 'Comment Added Successfully !');
                }
                if ($comment->isApproved() === false) {
                    $notificationService->sendEmailIfNotApproved($comment);
                }
        $entityManagere = $this->getDoctrine()->getManager();
        $post = $entityManagere->getRepository(Post::class)->find($id);
        return $this->render('Post/detailedpost.html.twig', [
            'title' => $post->getTitle(),
            'date' => $post->getPostdate(),
            'photo' => $post->getPhoto(),
            'descripion' => $post->getDescription(),
            'posts' => $post,
            'comments' => $post,
            'id' => $post->getId(),
            'form'=>$form->createView(),
            ]);
        
    }

    #[Route('/state', name: 'stat', methods: ['GET', 'POST'])]
     public function statistiques(PostcommentRepository  $commandeRepository){
         // On va chercher toutes les catégories
 
         $commande = $commandeRepository->countBypost();
         $dates = [];
         $commandeCount = [];
         //$categColor = [];
         foreach($commande as $com){
             $dates[] = $com['postDate'];
             $commandeCount[] = $com['count'];
         }
         return $this->render('post/statistique.html.twig', [
             'dates' => json_encode($dates),
             'commandeCount' => json_encode($commandeCount),
         ]);
 
 
     }
    #[Route('/admin/postcomments/approve/{id}', name: 'admin_postcomments_approve')]
    public function approve(Postcomment $postComment): RedirectResponse
    {
        $postComment->setApproved(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($postComment);
        $entityManager->flush();

        return $this->redirectToRoute('comment');
    }
            #[Route('/search', name: 'search')]

            public function searchAction(Request $request)
            {
                $em = $this->getDoctrine()->getManager();
                $requestString = $request->get('q');
                $posts = $em->getRepository(Post::class)->findEntitiesByString($requestString);
                if (!$posts) {
                    $result['posts']['error'] = "Post not found :(";
                } else {
                         $result['posts'] = $this->getRealEntities($posts);
                       }
                return new Response(json_encode($result));
            }
              
            public function getRealEntities($posts)
            {
                foreach ($posts as $post) {
                    $realEntities[$post->getId()] = [$post->getPhoto(), $post->getTitle()];
                }
            return $realEntities;
            }
         #[Route('/delete', name: 'delete_comment')]

         public function deleteCommentAction(Request $request)
        {
            $id = $request->get('id');
            $em = $this->getDoctrine()->getManager();
            $comment = $em->getRepository(Postcomment::class)->find($id);
            $em->remove($comment);
            $em->flush();
            return $this->redirectToRoute('list_post');
        }
        #[Route('/deleteee', name: 'delete_commentadmin')]

        public function deleteCommentActionn(Request $request)
       {
           $id = $request->get('id');
           $em = $this->getDoctrine()->getManager();
           $comment = $em->getRepository(Postcomment::class)->find($id);
           $em->remove($comment);
           $em->flush();
           return $this->redirectToRoute('comment');
       }
      
        #[Route('/pag', name: 'app_post_pagination' )]
        public function khaledsss(Request $request, PostRepository $PostRepository, PaginatorInterface $paginator): Response
        {
            $Postpag = $PostRepository->findAll();
    

    
            return $this->redirectToRoute('list_post');
}            
}