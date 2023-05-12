<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\Reclamation1Type;
use App\Repository\ReclamationRepository;
use App\Repository\TypeReclamationRepository;
use App\Form\TraiterType; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Service\SendMailService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;


use Symfony\Component\Mime\Email;
use App\Form\SendMailType;

#[Route('/rec/admin')]
class RecAdminController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/', name: 'app_rec_admin_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository,TypeReclamationRepository $typereclamationRepository): Response
    {
        $rec=$reclamationRepository->findAll();
        $tr=$typereclamationRepository->findAll();
        $nbr=0;
        $nbt=0;
        foreach ($rec as $r){
            $nbr++;
        }
        foreach ($tr as $t){
            $nbt++;
        }
        $res=[];
        foreach($tr as $t){
            $idt=$t->getId();
            $i=0;
            foreach($rec as $r){ 
                $idr=$r->getIdTr()->getId();
                if ($idr==$idt){
                    $i++;
                }
            }
            $res[]=[
                'type'=> $t,
                'nb'=>$i,
            ];
        }
        $nbc=0;
        $nbtr=0;
        foreach($rec as $r){
            $e=$r->getEtat();
            if ($e=="traitement en cours"){
                $nbc++;
            }
            else{
                $nbtr++;
            }
        }
        return $this->render('rec_admin/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
            'res' => $res,
            'nbr' => $nbr,
            'nbc' => $nbc,
            'nbtr' => $nbtr,
        ]);
    }

    #[Route('/new', name: 'app_rec_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReclamationRepository $reclamationRepository ): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(Reclamation1Type::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);
            return $this->redirectToRoute('app_rec_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rec_admin/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rec_admin_show', methods: ['GET'])]
    public function show(Reclamation $reclamation, Request $request, MailerInterface $mailer): Response
    {
        
        return $this->render('rec_admin/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    #[Route('/{id}/email', name: 'sendMailToUser')]

    public function sendEmail(MailerInterface $mailer, Request $request, Reclamation $reclamation): Response
    {
        $form =$this->createForm(SendMailType::class,null);
        $form->handleRequest($request);
        $this->logger->info($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            dump('Formulaire soumis!');
            $message=$form->get('message')->getData();
            $subject=$form->get('subject')->getData();
            $email = (new Email())
                ->from('mohamedhaythem.sessi@esprit.tn')
                ->to((string)$reclamation->getEmail())
                ->subject((string)$subject)
                ->text('On essaye de traiter vottre reclamation!')
                ->html("<p>$message</p>");
                 $mailer->send($email);
                 $this->addFlash('success', 'Votre message a été envoyé avec succès !');
                
    

            return $this->redirectToRoute('app_rec_admin_index');
        }
        return $this->render('/admin/sendMail.html.twig', ['form' => $form->createView(),'user_email'=>$reclamation->getEmail()]);
    }
    

    #[Route('/{id}/edit', name: 'app_rec_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(Reclamation1Type::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_rec_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rec_admin/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_rec_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $reclamationRepository->remove($reclamation, true);
        }

        return $this->redirectToRoute('app_rec_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
