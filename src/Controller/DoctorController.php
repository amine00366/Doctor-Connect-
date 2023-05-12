<?php

namespace App\Controller;

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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MercurySeries\FlashyBundle\FlashyNotifier;
use App\Form\doctorregistre;
use App\Repository\DoctorRepository;
use App\Security\EmailVerifier;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Psr\Log\LoggerInterface;

class DoctorController extends AbstractController
{
    private $appointmentService;
    private $flashy;
    private $logger;
    private EmailVerifier $emailVerifier;


    public function __construct(AppointmentService $appointmentService,LoggerInterface $logger,FlashyNotifier $flashy,EmailVerifier $emailVerifier)
    {
        $this->appointmentService = $appointmentService;
        $this->logger = $logger;
        $this->flashy = $flashy;
        $this->emailVerifier = $emailVerifier;
    }
    #[Route('/doctor/approve/{id}', name: 'admin_postcomments_approve')]
   
    public function aapprove(Appointment $appointment): RedirectResponse
    {
        $appointment->setApproved(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($appointment);
        $entityManager->flush();

        return $this->redirectToRoute('app_show');
    }



////////// liste des rendez vous d'un médecin avec barre de recherche  
/**
     * @Route("/listR/{id}", name="app_recherche") 
     */
    public function listrecherche(AppointmentRepository $repository ,Request $request ,$id )
    {
        $reservations= $this->getDoctrine()->getRepository(Appointment::class)->findBy(['doctor' => $id]);

        ////
        $back = null;
      
        if($request->isMethod("POST")){
           
                
                    $type = $request->request->get('optionsearch');
                    $value = $request->request->get('Search');
                    switch ($type){
                        case 'categorie':
                            $reservations = $repository->findBycategorieee($value,$id);//
                            break;
                            case 'appointmentDate':
                            $reservations = $repository->findBydate($value,$id);//
                            break;
                           
                           
                            
                    }
                }
                
            if ( $reservations){
                $back = "success";
            }else{
                $back = "failure";
            }
            
       
    return $this->render('admin/njareb.html.twig',['reservations'=>$reservations,'back'=>$back]);
    }










   



///////////// liste des patient avec tri 

#[Route('/listpatienttri/{id}', name: 'app_liste_patienttri')]
public function listepatient(Request $request, AppointmentRepository $appointmentRepository, $id)
{
    // Récupérer tous les produits
    $reservations = $appointmentRepository->findBy(['user' => $id]); //doctor
    // Traiter la soumission du formulaire de tri
    $triDescendant = false;
    $triAscendant = false;
    if ($request->getMethod() === 'POST') {
        $triDescendant = $request->request->get('tri_descendant');
        $triAscendant = $request->request->get('tri_ascendant');
        $this->logger->info($triAscendant);
        $this->logger->info($triDescendant);
    }

    // Si le tri descendant est sélectionné
    if ($triDescendant) {
       

        $reservations = $appointmentRepository->findBydateDesc($id);
        
        
    }
    // Si le tri ascendant est sélectionné
    else if ($triAscendant) {
        $reservations = $appointmentRepository->findBydateAsc($id);
    }

    return $this->render('doctor/Tridate.html.twig', [
        'reservations' => $reservations,
        'tri_descendant' => $triDescendant,
        'tri_ascendant' => $triAscendant,
    ]);

}














// la liste des médecin 
    /**
     * @Route("/doctors", name="app_doctor_list")
     */
    public function list()
    {
        $doctors = $this->getDoctrine()->getRepository(Doctor::class)->findAll();

        return $this->render('doctor/list.html.twig', [
            'doctors' => $doctors,
            
        ]);
    }

 
   
// ajout d'un rendez vous avec un docteur spécifique par l'id du docteur
    /**
     * @Route("/reserve/{id}", name="reserve_appointment", methods={"GET","POST"})
     */

    public function profile(Doctor $doctor, Request $request, EntityManagerInterface $entityManager,FlashyNotifier $flashy,$id)
    {
        
        // Create the form object
        $form = $this->createForm(AppointmentType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the current user
       
            
            $typeReservation = $form->get('type')->getData();
            // Get the appointment date from the form data
            $appointmentDate = new \DateTime($form->get('appointmentDate')->getData()->format('Y-m-d H:i:s'));
            $datefin = (clone $appointmentDate)->add(new \DateInterval('PT30M'));
            // $appointmentDateString =  $appointmentDate->format('Y-m-d H:i:s');

            if (!$this->appointmentService->checkAppointmentAvailability($appointmentDate, $doctor->getId($id))) {
               /* throw new \Exception('The appointment date is not available');*/
                return $this->redirectToRoute('appointment_error'); 
                /*$this->addFlash('error', 'cette date est réserée');*/
               /* $this->flashy->success('Le rendez-vous non .', 'http://your-awesome-link.com');*/
                
                
            }

            // Create a new appointment entity
            $appointment = new Appointment();
            $appointment->setCategorie($form->get('categorie')->getData());
            $appointment->setDoctor($doctor);
            $appointment->setUser($this->getUser());
            $appointment->setAppointmentDate($appointmentDate);
            $appointment->setDatefin($datefin); 
            $appointment->setType($typeReservation);   
          
           

            // Save the appointment to the database
            $entityManager->persist($appointment);
            $entityManager->flush();
            $flashy>$this->addFlash('success', 'Le formulaire a été soumis avec succès.');
            return $this->redirectToRoute('appointment_success');
           
            

            
        }

        return $this->render('doctor/profile.html.twig', [
            'doctor' => $doctor,
            'form' => $form->createView(),
            'flashy' => $flashy
           
      
        ]);
    }


 
    /**
     * @Route("/appointment/success", name="appointment_success")
     */
    public function appointmenterror()
    {
        return $this->render('appointment/success.html.twig');
    }
     /**
     * @Route("/appointment/error", name="appointment_error")
     */
    public function appointmentSuccess()
    {
        return $this->render('appointment/error.html.twig');
    }
    
    
//// liste pour user des rendez vous 
  
    /**
     * @Route("/appointments/{id}", name="app_appointments", methods={"GET","POST"})
     */
    public function listAppointments(Request $request ,TypeappoinmentRepository $repository,$id)
    {   
        
       
        $typeappointment= $this->getDoctrine()->getRepository(Typeappoinment::class)->findAll();
        // Get the user's ID
        //once the user is logged in

        // Get the appointments by the user's ID
        $appointments = $this->getDoctrine()->getRepository(Appointment::class)->findBy(['user'=>$id]) ;

        // Render the template and pass the appointments as a parameter
        return $this->render('appointments/list.html.twig', [
            'appointments' => $appointments,
            'type' =>$typeappointment

            
        ]);
    }



     #[Route('/listbyCat/{id}', name: 'List_By_type')]
     public function show(AppointmentRepository $appoinmentrepo,$id,PaginatorInterface $paginator,Request $request)
     {
        
         $categorie = $this->getDoctrine()->getRepository(Typeappoinment::class)->find($id);
         $reservations= $appoinmentrepo->findBytype($categorie->getId());
         
         $reservations = $paginator->paginate(
            $reservations, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
         return $this->render('admin/listreservation.html.twig', [
             "type" => $categorie,
             'reservations' => $reservations,
            
             ]);
     }





// statistique 
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


     #[Route('/state', name: 'dash', methods: ['GET', 'POST'])]
     public function dash(AppointmentRepository  $commandeRepository,MedicamentRepository $medicamentRepository,CategorieRepository $catrepo){
         // On va chercher toutes les catégories
 
         $commande = $commandeRepository->countByDate();
         $dates = [];
         $commandeCount = [];
         //$categColor = [];
         foreach($commande as $com){
             $dates[] = $com['appointmentDate'];
             $commandeCount[] = $com['count'];
         }

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







//les détail d'une réservation j'ai intégrer la map 
    /**
     * @Route("/appointment/{id}", name="app_appointment_detail")
     */
    public function appointmentDetails(Appointment $appointment, Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('appointments/appointment.html.twig', [
            'appointment' => $appointment,

        ]);
    }
// redirection des doctors 
     /**
     * @Route("/routefordoctor", name="routefordoctor")
     */
    public function routefordoctor()
    {
        return $this->render('basefordoctor.html.twig', []);
    }
    // redirection de users 
     /**
     * @Route("/routeforuser", name="routeforuser")
     */
    public function routeforuser()
    {
        return $this->render('base.html.twig', []);
    }



    //liste reservation des rendez-vous d'un médecin 
    #[Route('/reservation/{id}', name: 'app_show')]
    public function RES (Doctor $user, AppointmentRepository $repository): Response
    {
        $reservations = $repository->findBy(['doctor' => $user]);
        
        return $this->render('appointments/listmed.html.twig', [
            'reservations' => $reservations,
            /*'user' => $user,*/
        ]);
    }

 //Liste d'un médecin avec tri par date
 #[Route('/listmedtri/{id}', name: 'app_store_dateTri')]
 public function liste(Request $request, AppointmentRepository $appointmentRepository, $id)
 {
     $this->logger->info("test");
     // Récupérer tous les produits
     $reservations = $appointmentRepository->findBy(['doctor' => $id]);
     // Traiter la soumission du formulaire de tri
     $triDescendant = false;
     $triAscendant = false;
     if ($request->getMethod() === 'POST') {
         $triDescendant = $request->request->get('tri_descendant');
         $triAscendant = $request->request->get('tri_ascendant');
         $this->logger->info($triAscendant);
         $this->logger->info($triDescendant);
     }

     // Si le tri descendant est sélectionné
     if ($triDescendant) {
        

         $reservations = $appointmentRepository->findBydateDesc($id);
         
         
     }
     // Si le tri ascendant est sélectionné
     else if ($triAscendant) {
         $reservations = $appointmentRepository->findBydateAsc($id);
     }

     return $this->render('doctor/Tridate.html.twig', [
         'reservations' => $reservations,
         'tri_descendant' => $triDescendant,
         'tri_ascendant' => $triAscendant,
     ]);
 


}
    ////////////// liste des tous les rendezvous pour administrateur


    #[Route('/listeall', name: 'listeall')]
    public function listeall (AppointmentRepository $repository,PaginatorInterface $paginator,Request $request): Response
    {
        $typeappointment= $this->getDoctrine()->getRepository(Typeappoinment::class)->findAll();
        $reservations = $repository->findAll();
        $reservations = $paginator->paginate(
            $reservations, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render('admin/listreservation.html.twig', [
            'reservations' => $reservations,
            'type' =>$typeappointment
            /*'user' => $user,*/
        ]);
    }

    //delete une reservation pour patient 
    #[Route('/delete_reserv/{id}', name: 'delete_res')]

    public function deletepostAction(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Appointment::class)->find($id);
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('app_home');
    }

    //delete une reservation pour  
    #[Route('/delete_reservation/{id}', name: 'delete_reservation')]

    public function deleteReservationAction(Request $request): Response
    {
        $id = $request->get('id');
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Appointment::class)->find($id);
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('app_home');
    }






    //////////////   calendrier d'un médecin   

    #[Route('/calen/{id}', name: 'app_calen', methods: ['GET'])]
    public function calen(Doctor $user, AppointmentRepository $repository)
    {
        $reservationsapprouve = $repository->findBy(['doctor' => $user,'approved'=>1]);
        $reservationsnonapprouve = $repository->findBy(['doctor' => $user,'approved'=>0]); //,'approved'=>1

       

        $rdvs = [];

        foreach ($reservationsapprouve as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getAppointmentDate()->format('Y-m-d H:i:s'),
                'end' => $event->getDatefin()->format('Y-m-d H:i:s'),
                'title' => $event->getCategorie(),
                'backgroundColor' => 'green',

            ];
        }
        foreach ($reservationsnonapprouve as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getAppointmentDate()->format('Y-m-d H:i:s'),
                'end' => $event->getDatefin()->format('Y-m-d H:i:s'),
                'title' => $event->getCategorie(),
                'backgroundColor' => 'red',

            ];
        }
        
        $data = json_encode($rdvs);
        //dd($data);
        

        return $this->render('doctor/showCalendarperdoctor.html.twig', compact('data'));
    }


   /**
     * @Route("/{id}/edit", name="update-res", methods={"GET","POST"})
     */
    public function edit(Request $request, Appointment $auteur): Response
    {
        $form = $this->createForm(AppointmentType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('appointments/edit.html.twig', [
            'auteur' => $auteur,
            'form' => $form->createView(),
        ]);
    }
    
    
    
    
    
      /**
  * @Route("/{id}/editdoctor", name="update-resdoc", methods={"GET","POST"})
      */
 public function editdoc(Request $request, Appointment $auteur): Response
    {
        $form = $this->createForm(AppointmentType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('routefordoctor');
        }

        return $this->render('appointments/editd.html.twig', [
            'auteur' => $auteur,
            'form' => $form->createView(),
        ]);
    }



    /// calendrier  des tous les Rendez Vous 
    #[Route('/cal', name: 'app_cal', methods: ['GET'])]
    public function cal(AppointmentRepository $appointmentRepository)
    {
        $events = $appointmentRepository->findAll();

        $rdvs = [];

        foreach ($events as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getAppointmentDate()->format('Y-m-d H:i:s'),
                'end' => $event->getDatefin()->format('Y-m-d H:i:s'),
                'title' => $event->getCategorie(),
            ];
        }

     

        $data = json_encode($rdvs);

        return $this->render('doctor/showCalendar.html.twig', compact('data'));
    }








////// ajout d'un docteur avec un map API Map Box
    /**
     * @Route("/doctor/add", name="doctor_add")
     */
    public function add(Request $request,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $doctor = new Doctor();

        $form = $this->createForm(DoctorType::class, $doctor);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $doctor->setRoleperm("doctor");
            $doctor->setPassword(
                $userPasswordHasher->hashPassword(
                    $doctor,
                    $form->get('password')->getData()
                    
                ));
            $entityManager->persist($doctor);
            $entityManager->flush();

            return $this->redirectToRoute('app_doctor_list');
        }

        $mapboxAccessToken = 'Votre map Box access token ';

        return $this->render('doctor/new.html.twig', [
            'form' => $form->createView(),
            'mapbox_access_token' => $mapboxAccessToken,
        ]);
    }

    #[Route('/dd', name: 'app_doctor_index',)]
    
    public function index(DoctorRepository $doctorRepository, Request $request, ): Response

    {
        $user = $this->getUser();

        $doctor=$doctorRepository->findAll(); 
        $back = null;
        if($request->isMethod("POST")){
            if ( $request->request->get('optionsRadios')){
                $SortKey = $request->request->get('optionsRadios');
                switch ($SortKey){
                    case 'Specialite':
                        $doctor = $doctorRepository->SortBySpecialite();
                        break;
                        case 'id':
                            $doctor = $doctorRepository->SortByid();
                            break;
                           
                        
                }
                
           
            }
            
            else
            {
                $type = $request->request->get('optionsearch');
                $value = $request->request->get('Search');
                switch ($type){
                    case 'Specialite':
                        $doctor = $doctorRepository->findBySpecialite($value);
                        break;

                    

                }
            }

            if ( $doctor){
                $back = "success";
            }else{
                $back = "failure";
            }
        }
        return $this->render('doctor/index.html.twig', [
            'doctors' => $doctor,'back'=>$back,
            'user'=>$user
        ]);
    }

    #[Route('/new', name: 'app_doctor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DoctorRepository $doctorRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $doctor = new Doctor();
        $form = $this->createForm(doctorregistre::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctor->setRoleperm("doctor");
            $doctor->setPassword(
                $userPasswordHasher->hashPassword(
                    $doctor,
                    $form->get('plainPassword')->getData()
                )
            );
        
            $entityManager->persist($doctor);
            $entityManager->flush();

            // generate a signed url and email it to the doctor
           
            
            return $this->redirectToRoute('app_doctor_index');
        }

        return $this->render('doctor/new.html.twig', [
            'doctor' => $doctor,
            'form' => $form->createView(),
        ]);
    }

    // #[Route('/verify/email', name: 'app_doctor_verify_email')]
    // public function verifyDoctorEmail(Request $request, TranslatorInterface $translator): Response
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        
    //     try {
    //         $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
    //     } catch (VerifyEmailExceptionInterface $exception) {
    //         $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

    //         return $this->redirectToRoute('app_doctor_verify_email');
    //     }
   
    #[Route('/app/{id}', name: 'app_doctor_show', methods: ['GET'])]

     public function showD (Doctor $doctor): Response
    {
        return $this->render('doctor/show.html.twig', [
            'doctor' => $doctor,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_doctor_edit', methods: ['GET', 'POST'])]
    public function editD(Request $request, Doctor $doctor, DoctorRepository $doctorRepository): Response
    {
        $form = $this->createForm(DoctorType::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctorRepository->save($doctor, true);

            return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('doctor/edit.html.twig', [
            'doctor' => $doctor,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_doctor_delete', methods: ['POST'])]
    public function delete(Request $request, Doctor $doctor, DoctorRepository $doctorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$doctor->getId(), $request->request->get('_token'))) {
            $doctorRepository->remove($doctor, true);
        }

        return $this->redirectToRoute('app_doctor_index', [], Response::HTTP_SEE_OTHER);
    }
}




   


