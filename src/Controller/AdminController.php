<?php

namespace App\Controller;
use App\Entity\Post;
use App\Entity\Postcomment;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Appointment;
use App\Entity\Doctor;
use App\Entity\Typeappoinment;
use App\Entity\User;
use App\Form\AppointmentType;
use App\Form\DoctorType;
use App\Repository\AppointmentRepository;
use App\Repository\CategorieRepository;
use App\Repository\MedicamentRepository;
use App\Repository\TypeappoinmentRepository;
use App\Service\AppointmentService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Form\doctorregistre;
use App\Repository\DoctorRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Psr\Log\LoggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/test.html.twig');
    }
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('admin/profile.html.twig');
    }
    #[Route('/dash', name: 'stat', methods: ['GET', 'POST'])]
    public function statistiques(AppointmentRepository  $commandeRepository,MedicamentRepository $medicamentRepository,CategorieRepository $catrepo){
        // On va chercher toutes les catégories

        $commande = $commandeRepository->countByDate();
        $dates = [];
        $commandeCount = [];
        //$categColor = [];
        foreach($commande as $com){
            $dates[] = $com['appointmentDate'];
            $commandeCount[] = $com['count'];
        }
///////////////
        $med=$medicamentRepository->findAll();
        $cat=$catrepo->findAll();
        $nbm=0;
        $nbc=0;
        foreach ($med as $m){
            $nbm++;
        }
        foreach ($cat as $c){
            $nbc++;
        }
        $res=[];
        foreach($cat as $c){
            $idc=$c->getId();
            $i=0;
            foreach($med as $m){ 
                $idm=$m->getIdCategorie()->getId();
                if ($idc==$idm){
                    $i++;
                }
            }
            $res[]=[
                'cat'=> $c,
                'nb'=>$i,
            ];
        }
        return $this->render('admin/dashboard.html.twig', [
            'dates' => json_encode($dates),
            'commandeCount' => json_encode($commandeCount),
            'res' => $res,
        ]);


    }

    #[Route('/tables', name: 'app_tables')]

    public function listadmin(Request $request,PostRepository $repository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)->findAll();
        $back = null;
            
            if($request->isMethod("POST")){
                if ( $request->request->get('optionsRadios')){
                    $SortKey = $request->request->get('optionsRadios');
                    switch ($SortKey){
                        case 'title':
                            $posts = $repository->SortBytitle();
                            break;
             
                    }
                }
                else
                {
                    $type = $request->request->get('optionsearch');
                    $value = $request->request->get('Search');
                    switch ($type){
                        case 'title':
                            $posts = $repository->findBytitle($value);
                            break;
    
                        
    
                    }
                }

                if ( $posts){
                    $back = "success";
                }else{
                    $back = "failure";
                }
            }
        return $this->render('admin/table-basic.html.twig', [
            "posts" => $posts,
            'back'=>$back
        ]);
    }
    #[Route('/tablecomment', name: 'comment')]

    public function comment(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager->getRepository(Postcomment::class)->findAll();
        return $this->render('admin/tablecomment.html.twig', [
            "comments" => $comment,
        ]);
    }
       
}

       
