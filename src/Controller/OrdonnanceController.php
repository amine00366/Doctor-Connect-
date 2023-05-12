<?php

namespace App\Controller;

    //const PATH_PDF = "../pdf/";
use Knp\Snappy\Pdf;
use App\Entity\Ordonnance;
use App\Form\OrdonnanceType;
use App\Repository\OrdonnanceRepository;
use App\Repository\MedicamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
Use App\Controller\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Environment;

#[Route('/ordonnance')]
class OrdonnanceController extends AbstractController
{
    const PATH_PDF = "../pdf/";

    private $twig;
 private $snappy;

    public function __construct(Environment $twig,Pdf $snappy )
    {
        $this->twig = $twig;
        $this->snappy = $snappy;
    }
   
   /* #[Route('/', name: 'app_ordonnance_index', methods: ['GET'])]
    public function index9(OrdonnanceRepository $ordonnanceRepository): Response
    {
        return $this->render('ordonnance/index.html.twig', [
            'ordonnances' => $ordonnanceRepository->findAll(),
        ]);
    }

*/
#[Route("addTypeRecJSON/new", name: "addTypeRecJSON")]
public function addTypeRecJSON(Request $request, EntityManagerInterface $entityManager, NormalizerInterface $Normalizer)
{
$Ordonnances= new Ordonnance();
$Ordonnances->setNomMedicament($request->get('Nom_Medicament'));

        $Ordonnances->setFrequence($request->get('frequence'));   
        $Ordonnances->setDose($request->get('dose'));

    //  $Ordonnances->setDateCreation($request->get('date_creation'));
      $dateCreation = new \DateTime($request->get('date_creation'));
$Ordonnances->setDateCreation($dateCreation);

      /*  $date = new DateTime($request->get('getDateCreation'));
         $Ordonnances->setDateCreation($date); */


       // $medicament = new Medicament();
       // $Ordonnances->setMedicaments($medicament);
     // $id_consultation_id = $request->get('id_consultation_id');
      // $typeReclamation = $ConsultationRepository->find($id_consultation_id);
      // $Ordonnances->setIdConsultation($typeReclamation);


      //  $Ordonnances->setMedicaments($request->get('nonMedicament'));
      //  $Ordonnances->setIdConsultation($request->get('id_Consultation'));
       // $Ordonnances->setNomMedecin($request->get('NomMedecin'));


$entityManager->persist($Ordonnances);
$entityManager->flush();

$jsonContent = $Normalizer->normalize($Ordonnances, 'json', ['groups' => 'Ordonnances']);
return new Response(json_encode($jsonContent));
}

    #[Route('/', name: 'app_ordonnance_index', methods: ['GET'])]
        public function index(Request $request, OrdonnanceRepository $ordonnanceRepository, PaginatorInterface $paginator): Response
    {
        $ordonnances = $paginator->paginate(
            $ordonnanceRepository->findAll(),
            $request->query->getInt('page', 1),
           3 // Nombre d'éléments affichés par page
        );

        return $this->render('ordonnance/index.html.twig', [
            'ordonnances' => $ordonnances,
        ]);
    }

    #[Route('/newjson', name: 'app_ordonnance_newjson')]
    public function newjson( OrdonnanceRepository $OrdonnanceRepository, NormalizerInterface $normalizer )
{
    $Ordonnances=$OrdonnanceRepository ->findAll();
    $OrdonnanceNprmalises = $normalizer ->normalize($Ordonnances, 'json ');
    $json =json_encode($OrdonnanceNprmalises);
    return new Response($json);
}
    #[Route('/new', name: 'app_ordonnance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrdonnanceRepository $OrdonnanceRepository,MedicamentRepository $med): Response
    {
        $medicamentsSelectionnes = $request->request->get('medicament');
        if( $medicamentsSelectionnes!=[]){
        $medicamentsString = implode('|', $medicamentsSelectionnes);
        }
        $Ordonnance = new Ordonnance();
        $form = $this->createForm(OrdonnanceType::class, $Ordonnance);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $Ordonnance->setNomMedicament($medicamentsString);
            $OrdonnanceRepository->save($Ordonnance, true);
            
            return $this->redirectToRoute('app_ordonnance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ordonnance/new.html.twig', [
            'medicaments' => $med->findAll(),
            'Ordonnance' => $Ordonnance,
            'form' => $form,
        ]);
    }
   /*  #[Route('/new', name: 'pdf_generator', methods: ['GET', 'POST'])]
    public function new(Request $request, OrdonnanceRepository $entityManager): Response
    {
        $ordonnance = new Ordonnance();
        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->save($ordonnance, true);
            //$entityManager->persist($ordonnance);
            //$entityManager->flush();
            return $this->redirectToRoute('app_ordonnance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ordonnance/pdf.html.twig', [
            'ordonnance' => $ordonnance,
            'form' => $form,
        ]);
    }
*/
    #[Route('/{id}', name: 'app_ordonnance_show', methods: ['GET'])]
    public function show(Ordonnance $ordonnance): Response
    {
        return $this->render('ordonnance/show.html.twig', [
            'ordonnance' => $ordonnance,
        ]);
    }
     
   #[Route('/imprimer/{id}', name:'app_ordonnance_imprimmaison')]
    public function imprimer(OrdonnanceRepository $OrdonnanceRepository, int $id): Response


   { 
    $ordonnance=$OrdonnanceRepository->find($id);
    $pdf = $this->getOrdonnancePdf($ordonnance);

    return new Response($pdf,200,array(
        'Content-Type' => 'application/pdf',
        'Content-disposition' => 'attachment; filename=page-ordonance.pdf'
    ));

    }
    public function getOrdonnancePdf(Ordonnance $ordonnance){
        $html = $this->twig->render(
            'ordonnance/pdf.html.twig',
            array(
                'ordonnance' => $ordonnance,
            )
        );
        $response = new PdfResponse(
            $this->snappy->getOutputFromHtml($html,array(
                'margin-top'    => 10,
                'margin-right'  => 10,
                'margin-bottom' => 10,
                'margin-left'   => 10,
                'footer-spacing' => -5,
                'footer-font-name' => 'Calibri',
            )),
            'Ordonnance.pdf'
        );

        return $response;


    }


    #[Route('/{id}/edit', name: 'app_ordonnance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ordonnance $ordonnance, OrdonnanceRepository $ordonnanceRepository): Response
    {
        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordonnanceRepository->save($ordonnance, true);

            return $this->redirectToRoute('app_ordonnance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ordonnance/edit.html.twig', [
            'ordonnance' => $ordonnance,
            'form' => $form,
        ]);
    }
 
    #[Route('/{id}', name: 'app_ordonnance_delete', methods: ['POST'])]
    public function delete(Request $request, Ordonnance $ordonnance, OrdonnanceRepository $ordonnanceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ordonnance->getId(), $request->request->get('_token'))) {
            $ordonnanceRepository->remove($ordonnance, true);
        }

        return $this->redirectToRoute('app_ordonnance_index', [], Response::HTTP_SEE_OTHER);
    }
  
}
