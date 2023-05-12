<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Entity\Medicament;
use App\Form\Medicament1Type;
use App\Repository\MedicamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Environment;

#[Route('/admin/medicament')]
class AdminMedicamentController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
   
    
}
    #[Route('/', name: 'app_admin_medicament_index', methods: ['GET'])]
    public function index(MedicamentRepository $medicamentRepository,CategorieRepository $catrepo,PaginatorInterface $paginator,Request $request): Response
    { 
        $medi = $paginator->paginate(
        $medicamentRepository->findAll(),
        $request->query->getInt('page', 1),
       3 // Nombre d'éléments affichés par page
    );


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

        $this->logger->info($nbm);
        $this->logger->info($nbc);
        return $this->render('admin_medicament/index.html.twig', [
            'medicaments' => $medi,
            'res' => $res,
        ]);
    }

    #[Route('/new', name: 'app_admin_medicament_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MedicamentRepository $medicamentRepository): Response
    {
        $medicament = new Medicament();
        $form = $this->createForm(Medicament1Type::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->getClientOriginalExtension();
                $imageFile->move(
                    $this->getParameter('image_path'),
                    $newFilename
                );
                $medicament->setImage($newFilename);
            }
            $medicamentRepository->save($medicament, true);

            return $this->redirectToRoute('app_admin_medicament_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_medicament/new.html.twig', [
            'medicament' => $medicament,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_medicament_show', methods: ['GET'])]
    public function show(Medicament $medicament): Response
    {
        return $this->render('admin_medicament/show.html.twig', [
            'medicament' => $medicament,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_medicament_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Medicament $medicament, MedicamentRepository $medicamentRepository): Response
    {
        $form = $this->createForm(Medicament1Type::class, $medicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medicamentRepository->save($medicament, true);

            return $this->redirectToRoute('app_admin_medicament_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_medicament/edit.html.twig', [
            'medicament' => $medicament,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_medicament_delete', methods: ['POST'])]
    public function delete(Request $request, Medicament $medicament, MedicamentRepository $medicamentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medicament->getId(), $request->request->get('_token'))) {
            $medicamentRepository->remove($medicament, true);
        }

        return $this->redirectToRoute('app_admin_medicament_index', [], Response::HTTP_SEE_OTHER);
    }
}
