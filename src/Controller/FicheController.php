<?php

namespace App\Controller;
use App\Service\Twilio;
use App\Entity\Fiche;
use App\Form\FicheType;
 use App\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Environment;
#[Route('/fiche')]
class FicheController extends AbstractController
{
  /*  #[Route('/', name: 'app_fiche_index', methods: ['GET'])]
    public function index6(FicheRepository $ficheRepository): Response
    {
        return $this->render('fiche/index.html.twig', [
            'fiches' => $ficheRepository->findAll(),
             
        ]);
    }
    */
    #[Route('/', name: 'app_fiche_index', methods: ['GET'])]
    public function index(Request $request, FicheRepository $ficheRepository, PaginatorInterface $paginator): Response
{
    $fiches = $paginator->paginate(
        $ficheRepository->findAll(),
        $request->query->getInt('page', 1),
       3 // Nombre d'éléments affichés par page
    );

    return $this->render('fiche/index.html.twig', [
        'fiches' => $fiches,
    ]);
}
    #[Route('/search', name: 'fiche_search', methods: ['POST','GET'])]
    public function search(Request $request,FicheRepository $ficheRepository): JsonResponse
    {
        $query = $request->query->get('q');
        if ($query==""){
            $results = [];
            return $this->json($results);
        }
        $fiches = $ficheRepository->findByExampleField($query);

        $results = [];
        foreach ($fiches as $fiche) {
            $results[] = [
                'note' => $fiche->getNote()
            ];
        }
        return $this->json($results);
    }
    #[Route('/new', name: 'app_fiche_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FicheRepository $FicheRepository): Response
    {
        $fiche = new Fiche();
        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FicheRepository->save($fiche, true);

            return $this->redirectToRoute('app_fiche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fiche/new.html.twig', [
            'fiche' => $fiche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fiche_show', methods: ['GET'])]
    public function show(Fiche $fiche): Response
    {
        return $this->render('fiche/show.html.twig', [
            'fiche' => $fiche,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fiche_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fiche $fiche, FicheRepository $FicheRepository): Response
    {
        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $FicheRepository->save($fiche ,true);

            return $this->redirectToRoute('app_fiche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fiche/edit.html.twig', [
            'fiche' => $fiche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fiche_delete', methods: ['POST'])]
    public function delete(Request $request, Fiche $fiche, FicheRepository $FicheRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fiche->getId(), $request->request->get('_token'))) {
            $FicheRepository->remove($fiche);
            $FicheRepository->flush();
        }

        return $this->redirectToRoute('app_fiche_index', [], Response::HTTP_SEE_OTHER);
    }
/*  #[Route("/send-sms/{patientNumero}", name: "app_sms_send")]
   
    public function sendSms(Twilio $twilio, $patientNumero)
    {
        $message = "Merci pour votre confiance.";

        $twilio->messages->create($patientNumero, ['from' => '+15746525201', 'body' => $message]);

        return new Response("SMS envoyé.");
    }
  */
}
